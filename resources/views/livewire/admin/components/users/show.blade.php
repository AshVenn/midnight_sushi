<x-card title="Détails de l'Utilisateur">
    <div><strong>Nom Complet :</strong> {{ $user->first_name }} {{ $user->last_name }}</div>
    <div><strong>Email :</strong> {{ $user->email }}</div>
    <div><strong>Rôle :</strong> {{ ucfirst($user->role) }}</div>
    <div><strong>Email Vérifié :</strong> {{ $user->email_verified_at ? 'Oui' : 'Non' }}</div>
</x-card>
