<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('admin/'); ?>">Administrator</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">Data User</a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="<?= base_url('admin/profil'); ?>"><i class="fas fa-user-cog text-success"></i> Profil</a></li>
                        <hr>
                        <li><a class="dropdown-item" href="<?= base_url('admin/owner'); ?>"><i class="fas fa-user-tie text-success"></i> Data Owner</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/konsumen'); ?>"><i class="fas fa-user text-success"></i> Data Konsumen</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('admin/bidan'); ?>"><i class="fas fa-user-md text-success"></i> Data Bidan</a></li>
                    </ul>
                </li>
                <li>
                    <a href="<?= base_url('admin/cabang'); ?>" class="nav-link">Cabang</a>
                </li>
                <li>
                    <a href="<?= base_url('admin/layanan'); ?>" class="nav-link">Layanan</a>
                </li>
            </ul>
        </div>
        <?php if (session()->get('logged_in') == true) : ?>
            <button class="btn btn-success" id="logout">Keluar</button>
        <?php else : ?>
            <button class="btn btn-success" id="logout">
                <a class="text-decoration-none" href="<?= "/login" ?>">Masuk</a>
            </button>
        <?php endif; ?>
    </div>
</nav>