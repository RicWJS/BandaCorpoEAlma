{{-- laravel\resources\views\admin\partials\_sidebar.blade.php --}}
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin</h2>
    </div>
    <ul class="admin-nav">
        {{-- Para o Dashboard, verificamos 'admin.dashboard' e 'admin.dashboard.index' --}}
        <li class="{{ Route::is('admin.dashboard*') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        {{-- Para Editar Seções, verificamos qualquer rota que comece com 'admin.forms.' --}}
        <li class="{{ Route::is('admin.forms.*') ? 'active' : '' }}">
            <div class="nav-item has-submenu">
                <i class="fas fa-edit"></i> Editar Seções
            </div>
            {{-- Adiciona a classe 'open' para que o submenu já carregue aberto --}}
            <ul class="submenu {{ Route::is('admin.forms.*') ? 'open' : '' }}">
                <li class="{{ Route::is('admin.forms.bannerSection') ? 'active-submenu' : '' }}"><a href="{{ route('admin.forms.bannerSection') }}">Banner Principal</a></li>
                <li class="{{ Route::is('admin.forms.spotifySection') ? 'active-submenu' : '' }}"><a href="{{ route('admin.forms.spotifySection') }}">Embed do Spotify</a></li>
                {{-- <li class="{{ Route::is('admin.forms.contactPage') ? 'active-submenu' : '' }}"><a href="{{ route('admin.forms.contactPage') }}">Página de Contato</a></li> --}}
            </ul>
        </li>
        
        <li class="{{ Route::is('admin.categories.*') ? 'active' : '' }}">
            <div class="nav-item has-submenu">
                <i class="fas fa-tags"></i> Categorias
            </div>
            <ul class="submenu {{ Route::is('admin.categories.*') ? 'open' : '' }}">
                <li class="{{ Route::is('admin.categories.index') ? 'active-submenu' : '' }}"><a href="{{ route('admin.categories.index') }}">Todas as Categorias</a></li>
                <li class="{{ Route::is('admin.categories.create') ? 'active-submenu' : '' }}"><a href="{{ route('admin.categories.create') }}">Adicionar Nova</a></li>
            </ul>
        </li>

        {{-- Para Notícias, verificamos qualquer rota que comece com 'admin.posts.', o que inclui index, create, show, edit, etc. --}}
        <li class="{{ Route::is('admin.posts.*') ? 'active' : '' }}">
            <div class="nav-item has-submenu">
                <i class="fas fa-newspaper"></i> Notícias
            </div>
            {{-- Adiciona a classe 'open' para que o submenu já carregue aberto --}}
            <ul class="submenu {{ Route::is('admin.posts.*') ? 'open' : '' }}">
                <li class="{{ Route::is('admin.posts.index') ? 'active-submenu' : '' }}"><a href="{{ route('admin.posts.index') }}">Todas as Notícias</a></li>
                <li class="{{ Route::is('admin.posts.create') ? 'active-submenu' : '' }}"><a href="{{ route('admin.posts.create') }}">Publicar Nova Notícia</a></li>
            </ul>
        </li>

        {{-- Para Configurações, verificamos a rota 'admin.settings' --}}
        {{-- <li class="{{ Route::is('admin.settings') ? 'active' : '' }}">
            <a href="{{ route('admin.settings') }}">
                <i class="fas fa-cog"></i> Configurações
            </a>
        </li> --}}

        <li>
            {{-- A rota de Logout não precisa de estado ativo --}}
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </form>
        </li>
    </ul>

    {{-- Este script permanece o mesmo. Ele cuida do clique para abrir/fechar o submenu --}}
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const submenuToggles = document.querySelectorAll('.has-submenu');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const submenu = this.nextElementSibling;
                    if (submenu && submenu.classList.contains('submenu')) {
                        submenu.classList.toggle('open');
                    }
                });
            });
        });
    </script>
    @endpush
</aside>
