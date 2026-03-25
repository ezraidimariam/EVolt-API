@extends('layouts.app')

@section('title', 'Connexion - EVolt')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-green-50 to-blue-50">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-green-100">
                <i class="fas fa-charging-station text-green-600 text-xl"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Connectez-vous à EVolt
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Accédez à votre espace de gestion de recharge
            </p>
        </div>
        
        <form class="mt-8 space-y-6" id="loginForm">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" 
                           placeholder="Adresse email">
                </div>
                <div>
                    <label for="password" class="sr-only">Mot de passe</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-green-500 focus:border-green-500 focus:z-10 sm:text-sm" 
                           placeholder="Mot de passe">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                        Se souvenir de moi
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" id="loginBtn" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-green-500 group-hover:text-green-400"></i>
                    </span>
                    Se connecter
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Pas encore de compte? 
                    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-500">
                        S'inscrire
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('loginBtn');
    const originalText = btn.innerHTML;
    
    // Loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Connexion...';
    btn.disabled = true;
    
    try {
        const response = await axios.post('/login', {
            email: document.getElementById('email').value,
            password: document.getElementById('password').value
        });
        
        // Store token
        localStorage.setItem('authToken', response.data.token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
        
        // Success message and redirect
        alert('Connexion réussie!');
        window.location.href = '/dashboard';
        
    } catch (error) {
        console.error('Login error:', error);
        alert('Erreur de connexion: ' + (error.response?.data?.message || 'Vérifiez vos identifiants'));
        
        // Reset button
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});
</script>
@endsection
