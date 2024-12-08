<x-card title="User Details">
    <div><strong>Full Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</div>
    <div><strong>Email:</strong> {{ $user->email }}</div>
    <div><strong>Role:</strong> {{ ucfirst($user->role) }}</div>
    <div><strong>Email Verified:</strong> {{ $user->email_verified_at ? 'Yes' : 'No' }}</div>
</x-card>
