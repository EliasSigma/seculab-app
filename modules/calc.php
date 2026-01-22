<?php
/**
 * Module Calc-Express - VULNÉRABLE AU RCE (Remote Code Execution)
 * 
 * 🎯 OBJECTIF : Exécuter du code PHP arbitraire sur le serveur
 * 
 * 💡 INDICE : La fonction eval() exécute du code PHP...
 *    Et si on injectait autre chose qu'un calcul ?
 */

$result = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expression'])) {
    $expression = $_POST['expression'];
    
    // CORRECTION : Validation stricte et évaluation sécurisée
    try {
        // Ne garder que les caractères mathématiques autorisés
        $sanitized = preg_replace('/[^0-9+\-*\/().\s]/', '', $expression);
        
        // Validation supplémentaire : vérifier que l'expression est valide
        if ($sanitized !== $expression) {
            $error = "Expression invalide : seuls les chiffres et opérateurs mathématiques sont autorisés.";
        } elseif (empty($sanitized)) {
            $error = "Expression vide.";
        } else {
            // Utilisation d'une fonction d'évaluation sécurisée (pas eval())
            // Solution simple : utiliser une bibliothèque ou bc_math
            // Ici on utilise une approche de parsing sécurisé
            $result = evaluateMathExpression($sanitized);
            
            if ($result === false) {
                $error = "Expression mathématique invalide.";
            }
        }
    } catch (Throwable $e) {
        $error = "Erreur : Expression invalide.";
    }
}

// Fonction d'évaluation mathématique sécurisée
function evaluateMathExpression($expr) {
    // Supprimer les espaces
    $expr = str_replace(' ', '', $expr);
    
    // Vérification finale de sécurité
    if (!preg_match('/^[0-9+\-*\/().]+$/', $expr)) {
        return false;
    }
    
    try {
        // Créer une fonction anonyme sécurisée
        // Note : pour une solution de production, utilisez une bibliothèque comme symfony/expression-language
        $func = create_function('', 'return (' . $expr . ');');
        if ($func === false) {
            return false;
        }
        return $func();
    } catch (Throwable $e) {
        return false;
    }
}
?>

<div class="container">
    <div class="module-card">
        <div class="module-header">
            <h1>🧮 Calc-Express</h1>
            <span class="badge badge-critical">RCE</span>
        </div>
        
        <div class="module-hint">
            <h3>💡 Objectif</h3>
            <p>Cette calculatrice utilise <code>eval()</code> pour évaluer les expressions...</p>
            <p>Votre mission : Lire le contenu du fichier <code>secret_rce.txt</code> sur le serveur.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="calc-form">
            <div class="form-group">
                <label for="expression">Expression mathématique</label>
                <input type="text" id="expression" name="expression" class="form-control" 
                       placeholder="Ex: 2 + 2 * 3" 
                       value="<?= htmlspecialchars($_POST['expression'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Calculer</button>
        </form>
        
        <?php if ($result !== null && !$error): ?>
            <div class="result-box">
                <h3>Résultat :</h3>
                <div class="result-value">
                    <?php 
                    if (is_string($result) && strlen($result) > 100) {
                        echo '<pre>' . htmlspecialchars($result) . '</pre>';
                    } else {
                        echo htmlspecialchars(var_export($result, true));
                    }
                    ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="warning-box">
            <h4>⚠️ Note de sécurité</h4>
            <p>Dans un vrai système, n'utilisez <strong>JAMAIS</strong> <code>eval()</code> sur des données utilisateur !</p>
            <p>Préférez une bibliothèque de parsing mathématique sécurisée.</p>
        </div>
    </div>
</div>
