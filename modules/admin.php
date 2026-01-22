<?php
/**
 * Module Admin Panel - VULNÉRABLE À UNE ERREUR DE LOGIQUE
 * 
 * 🎯 OBJECTIF : Accéder au panel admin sans être administrateur
 * 
 * 💡 INDICE : La vérification admin se fait via un cookie...
 *    Les cookies peuvent être modifiés côté client !
 */

$isAdmin = false;

// CORRECTION : Vérification côté serveur via $_SESSION
$isAdmin = isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
?>

<div class="container">
    <div class="module-card">
        <div class="module-header">
            <h1>⚙️ Admin Panel</h1>
            <span class="badge badge-warning">Logic Error</span>
        </div>
        
        <div class="module-hint">
            <h3>💡 Objectif</h3>
            <p>L'accès à ce panel admin est protégé par une vérification côté client...</p>
            <p>Votre mission : Obtenir l'accès administrateur sans avoir les droits.</p>
        </div>
        
        <?php if (!$isAdmin): ?>
            <div class="access-denied">
                <div class="lock-icon">🔒</div>
                <h2>Accès Refusé</h2>
                <p>Cette zone est réservée aux administrateurs.</p>
                <p>Vous devez être connecté avec un compte administrateur pour accéder à cette page.</p>
            </div>
        <?php else: ?>
            
            <div class="admin-panel">
                <h2>👑 Bienvenue, Administrateur !</h2>
                
                <div class="admin-stats">
                    <div class="stat-card">
                        <h3>Utilisateurs</h3>
                        <span class="stat-value"><?= $db->query('SELECT COUNT(*) FROM users')->fetchColumn() ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Messages Wall</h3>
                        <span class="stat-value"><?= $db->query('SELECT COUNT(*) FROM wall_posts')->fetchColumn() ?></span>
                    </div>
                    <div class="stat-card">
                        <h3>Version PHP</h3>
                        <span class="stat-value"><?= PHP_VERSION ?></span>
                    </div>
                </div>
                
                <div class="admin-actions">
                    <h3>Actions administrateur</h3>
                    <button class="btn btn-danger" disabled>🗑️ Supprimer tous les messages</button>
                    <button class="btn btn-warning" disabled>🔄 Réinitialiser la base</button>
                    <button class="btn btn-info" disabled>📊 Exporter les logs</button>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="warning-box">
            <h4>⚠️ Note de sécurité</h4>
            <p>Ne jamais faire confiance aux données côté client (cookies, champs cachés, localStorage).</p>
            <p>La vérification d'accès doit se faire côté serveur via les sessions.</p>
        </div>
    </div>
</div>
