<?php
/**
 * Module User Bio - VULNÉRABLE À L'IDOR (Insecure Direct Object Reference)
 * 
 * 🎯 OBJECTIF : Accéder au profil de l'administrateur (ID 1)
 * 
 * 💡 INDICE : Le paramètre ?id= dans l'URL n'est pas vérifié...
 *    Vous pouvez voir n'importe quel profil en changeant l'ID !
 */

// CORRECTION : Vérification d'autorisation côté serveur
$requestedId = $_GET['id'] ?? null;

// Rediriger les utilisateurs non connectés
if (!isLoggedIn()) {
    flash('error', 'Vous devez être connecté pour voir les profils.');
    redirect('/auth');
}

// Si pas d'ID spécifié, rediriger vers son propre profil
if ($requestedId === null) {
    header('Location: /profile?id=' . $_SESSION['user_id']);
    exit;
}

$profile = null;
$isOwnProfile = false;

if ($requestedId) {
    // CORRECTION : Vérifier que l'utilisateur ne consulte que son propre profil
    // Ou qu'il est administrateur
    if ($_SESSION['user_id'] != $requestedId && !$_SESSION['is_admin']) {
        flash('error', 'Accès refusé : vous ne pouvez consulter que votre propre profil.');
        redirect('/profile?id=' . $_SESSION['user_id']);
    }
    
    $stmt = $db->prepare('SELECT id, username, bio, is_admin FROM users WHERE id = ?');
    $stmt->execute([$requestedId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $isOwnProfile = $_SESSION['user_id'] == $requestedId;
}
?>

<div class="container">
    <div class="module-card">
        <div class="module-header">
            <h1>👤 User Bio</h1>
            <span class="badge badge-warning">IDOR</span>
        </div>
        
        <div class="module-hint">
            <h3>💡 Objectif</h3>
            <p>Vous consultez actuellement votre propre profil.</p>
            <p>Votre mission : Trouver un moyen d'accéder au profil de l'administrateur qui contient un secret.</p>
        </div>
        
        <?php if ($profile): ?>
            <div class="profile-card <?= $profile['is_admin'] ? 'admin-profile' : '' ?>">
                <h2>
                    <?= htmlspecialchars($profile['username']) ?>
                    <?php if ($profile['is_admin']): ?>
                        <span class="badge badge-admin">👑 Administrateur</span>
                    <?php endif; ?>
                </h2>
                
                <div class="bio-section">
                    <h4>Biographie :</h4>
                    <p class="bio-content"><?= htmlspecialchars($profile['bio']) ?></p>
                </div>
                

            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <p>Aucun profil à afficher.</p>
                <?php if (!isLoggedIn()): ?>
                    <p>Connectez-vous pour voir votre profil.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="info-box">
            <h4>ℹ️ URL actuelle</h4>
            <p><code>/profile<?= isset($_GET['id']) ? '?id=' . htmlspecialchars($_GET['id']) : '' ?></code></p>
        </div>
    </div>
</div>
