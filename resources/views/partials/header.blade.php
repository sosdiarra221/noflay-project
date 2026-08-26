<!-- Begin Header -->
<header class="app-header" id="appHeader">
    <div class="container-fluid w-100">
        <div class="d-flex align-items-center">
            <div class="me-auto">
                <div class="d-inline-flex align-items-center gap-5">
                    <a href="index" class="fs-18 fw-semibold">
                        <img height="30" class="header-sidebar-logo-default d-none" alt="Logo" src="{{ asset('assets/images/logo-dark.png') }}">
                        <img height="30" class="header-sidebar-logo-light d-none" alt="Logo" src="{{ asset('assets/images/logo-light.png') }}">
                        <img height="30" class="header-sidebar-logo-small d-none" alt="Logo" src="{{ asset('assets/images/logo-md.png') }}">
                        <img height="30" class="header-sidebar-logo-small-light d-none" alt="Logo" src="{{ asset('assets/images/logo-md-light.png') }}">
                    </a>
                    <button type="button" class="vertical-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill" id="toggleSidebar">
                        <i class="bi bi-arrow-bar-left header-icon"></i>
                    </button>
                    <button type="button" class="horizontal-toggle btn btn-light-light text-muted icon-btn fs-5 rounded-pill d-none" id="toggleHorizontal">
                        <i class="ri-menu-2-line header-icon"></i>
                    </button>
                    <div class="header-dropdown d-flex align-items-center">
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                About
                            </button>
                            <div class="dropdown-menu dropdown-mega-xl p-0">
                                <div class="p-4 border-bottom d-flex align-items-center gap-4">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15">Ready to Begin Your Journey?</h6>
                                        <p class="mb-0 text-muted">Explore our resources to kickstart your experience!
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary">Documentation</a>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Apps</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="apps-chat">Chat</a></li>
                                                <li><a class="dropdown-item" href="apps-calendar">Calendar</a></li>
                                                <li><a class="dropdown-item" href="apps-email">Mailbox</a></li>
                                                <li><a class="dropdown-item" href="dashboard-ecommerce">Ecommerce</a></li>
                                                <li><a class="dropdown-item" href="index">Academy</a></li>
                                                <li><a class="dropdown-item" href="dashboard-logistic">Logistics</a></li>
                                                <li><a class="dropdown-item" href="dashboard-crm">CRM</a></li>
                                                <li><a class="dropdown-item" href="dashboard-project">Projects</a></li>
                                                <li><a class="dropdown-item" href="apps-invoice-detail">Invoices</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Pages</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="pages-profile">Profile</a></li>
                                                <li><a class="dropdown-item" href="../pages-timeline">Timeline</a></li>
                                                <li><a class="dropdown-item" href="../pages-blog-list">Blogs</a></li>
                                                <li><a class="dropdown-item" href="../pages-pricing">Pricing</a></li>
                                                <li><a class="dropdown-item" href="../pages-gallery">Gallery</a></li>
                                                <li><a class="dropdown-item" href="../pages-faqs">FAQ's</a></li>
                                                <li><a class="dropdown-item" href="../pages-sitemap">Sitemap</a></li>
                                                <li><a class="dropdown-item" href="../pages-search-result">Search Results</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Authentication & Error</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="../auth-signup">Sign Up</a></li>
                                                <li><a class="dropdown-item" href="../auth-signin">Sign In</a></li>
                                                <li><a class="dropdown-item" href="../auth-two-step-verify">Two Step Verification</a></li>
                                                <li><a class="dropdown-item" href="../auth-reset-password">Reset Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-create-password">Create Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-404">404</a></li>
                                                <li><a class="dropdown-item" href="../under-maintenance">Maintenance</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Authentication & Pages
                            </button>
                            <div class="dropdown-menu dropdown-mega-lg p-0">
                                <div class="p-4 d-flex align-items-center gap-4 bg-primary">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fs-15 text-bg-primary">Ready to Begin Your Journey?</h6>
                                        <p class="mb-0 text-white text-opacity-75">Explore our resources to kickstart
                                            your
                                            experience!</p>
                                    </div>
                                    <!-- <div class="flex-shrink-0">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary">Documentation</a>
                                    </div> -->
                                </div>
                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Apps</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="apps-chat">Chat</a></li>
                                                <li><a class="dropdown-item" href="apps-calendar">Calendar</a></li>
                                                <li><a class="dropdown-item" href="apps-email">Mailbox</a></li>
                                                <li><a class="dropdown-item" href="dashboard-ecommerce">Ecommerce</a></li>
                                                <li><a class="dropdown-item" href="index">Academy</a></li>
                                                <li><a class="dropdown-item" href="dashboard-logistic">Logistics</a></li>
                                                <li><a class="dropdown-item" href="dashboard-crm">CRM</a></li>
                                                <li><a class="dropdown-item" href="dashboard-project">Projects</a></li>
                                                <li><a class="dropdown-item" href="apps-invoice-detail">Invoices</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Pages</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="pages-profile">Profile</a></li>
                                                <li><a class="dropdown-item" href="../pages-timeline">Timeline</a></li>
                                                <li><a class="dropdown-item" href="../pages-blog-list">Blogs</a></li>
                                                <li><a class="dropdown-item" href="../pages-pricing">Pricing</a></li>
                                                <li><a class="dropdown-item" href="../pages-gallery">Gallery</a></li>
                                                <li><a class="dropdown-item" href="../pages-faqs">FAQ's</a></li>
                                                <li><a class="dropdown-item" href="../pages-sitemap">Sitemap</a></li>
                                                <li><a class="dropdown-item" href="../pages-search-result">Search Results</a></li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-4">
                                            <p class="mb-1 text-uppercase text-muted fs-12">Authentication & Error</p>
                                            <ul class="list-unstyled mb-0">
                                                <li><a class="dropdown-item" href="../auth-signup">Sign Up</a></li>
                                                <li><a class="dropdown-item" href="../auth-signin">Sign In</a></li>
                                                <li><a class="dropdown-item" href="../auth-two-step-verify">Two Step Verification</a></li>
                                                <li><a class="dropdown-item" href="../auth-reset-password">Reset Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-create-password">Create Password</a></li>
                                                <li><a class="dropdown-item" href="../auth-404">404</a></li>
                                                <li><a class="dropdown-item" href="../under-maintenance">Maintenance</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown pe-dropdown-mega pe-dropdown-hover">
                            <button class="btn pe-dropdown-button" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Help
                            </button>
                            <div class="dropdown-menu p-4">
                                <p class="mb-1 text-uppercase text-muted fs-12">Help & Support</p>
                                <ul class="list-unstyled mb-0">
                                    <li><a class="dropdown-item" href="javascript:void(0)">Documentation</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Quick Support</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Buy Now</a></li>
                                    <li><a class="dropdown-item" href="javascript:void(0)">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 d-flex align-items-center gap-1">
                <button type="button" class="btn header-btn d-none d-md-block" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">
                    <i class="bi bi-search"></i>
                </button>
                <button class="btn header-btn d-none d-md-block" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <i class="bi bi-gear"></i>
                </button>
                <button type="button" class="btn header-btn" id="toggleFullscreen" title="Plein écran">
                    <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>
                @include('partials.notifications-bell')
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn btn-icon header-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cart"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-mega-md header-dropdown-menu p-0">
                        <div>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Cart Items</h5>
                                    <a href="javascript:void(0)" id="cart-data">5 Item</a>
                                </div>
                                <ul class="list-unstyled list-none mb-0 p-4" id="header-cart-items-scroll">
                                    <li class="dropdown-item d-block py-2">
                                        <div class="d-flex items-start cart-dropdown-item">
                                            <img src="assets/images/product/img-01.png" class="avatar-md me-3" alt="img">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h6><a href="apps-ecommerce-products-details" class="text-reset">Branded Crop Top</a></h6>
                                                    <p class="mb-0 fs-12 text-muted">Quantity: <span>1 x $499</span></p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center px-2">
                                                <h6 class="m-0 fw-normal">$<span class="cart-item-price">499</span></h6>
                                            </div>
                                            <div class="ps-2 d-flex">
                                                <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-block py-2">
                                        <div class="d-flex items-start cart-dropdown-item">
                                            <img src="assets/images/product/img-02.png" class="avatar-md me-3" alt="img">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h6><a href="apps-ecommerce-products-details" class="text-reset">Stop Watch</a></h6>
                                                    <p class="mb-0 fs-12 text-muted">Quantity: <span>2 x $159</span></p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center px-2">
                                                <h6 class="m-0 fw-normal">$<span class="cart-item-price">318</span></h6>
                                            </div>
                                            <div class="ps-2 d-flex">
                                                <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-block py-2">
                                        <div class="d-flex items-start cart-dropdown-item">
                                            <img src="assets/images/product/img-03.png" class="avatar-md me-3" alt="img">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h6><a href="apps-ecommerce-products-details" class="text-reset">Jeens Shoes</a></h6>
                                                    <p class="mb-0 fs-12 text-muted">Quantity: <span>6 x $399</span></p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center px-2">
                                                <h6 class="m-0 fw-normal">$<span class="cart-item-price">2394</span></h6>
                                            </div>
                                            <div class="ps-2 d-flex">
                                                <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-block py-2">
                                        <div class="d-flex items-start cart-dropdown-item">
                                            <img src="assets/images/product/img-04.png" class="avatar-md me-3" alt="img">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h6><a href="apps-ecommerce-products-details" class="text-reset">Solder Less T-shirt</a></h6>
                                                    <p class="mb-0 fs-12 text-muted">Quantity: <span>3 x $259</span></p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center px-2">
                                                <h6 class="m-0 fw-normal">$<span class="cart-item-price">777</span></h6>
                                            </div>
                                            <div class="ps-2 d-flex">
                                                <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-block py-2">
                                        <div class="d-flex items-start cart-dropdown-item">
                                            <img src="assets/images/product/img-05.png" class="avatar-md me-3" alt="img">
                                            <div class="flex-grow-1">
                                                <div>
                                                    <h6><a href="apps-ecommerce-products-details" class="text-reset">Mens T-shirt</a></h6>
                                                    <p class="mb-0 fs-12 text-muted">Quantity: <span>2 x $299</span></p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center px-2">
                                                <h6 class="m-0 fw-normal">$<span class="cart-item-price">598</span></h6>
                                            </div>
                                            <div class="ps-2 d-flex">
                                                <button type="button" class="btn btn-sm"><i class="ri-close-fill fs-16"></i></button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="px-4 text-end">
                                    <a href="apps-ecommerce-cart"><button class="btn btn-outline-primary" type="button">View Cart</button></a>
                                    <a class="btn btn-primary view-checkout" href="apps-ecommerce-checkout">Checkout </a>
                                </div>
                            </div>
                        </div>
                    </ul>
                </div>
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="btn btn-icon header-btn p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                    </button>
                    <ul class="dropdown-menu header-dropdown-menu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                English
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/es.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Spanish
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/ru.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                French
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/us.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Russian
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/de.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                German
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/cn.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Chinese
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="javascript:void(0)">
                                <img src="assets/images/flag/sa.svg" alt="Flag Image" height="16" width="16" class="object-fit-cover rounded">
                                Arabic
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="dropdown pe-dropdown-mega d-none d-md-block">
                    <button class="header-profile-btn btn gap-1 text-start" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-btn btn position-relative">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="Avatar Image" class="img-fluid rounded-circle">
                            <span class="position-absolute translate-middle badge border border-light rounded-circle bg-success"><span class="visually-hidden">unread messages</span></span>
                        </span>
                        <div class="d-none d-lg-block pe-2">
                            <span class="d-block mb-0 fs-13 fw-semibold">{{ auth()->user()->name ?? 'Utilisateur' }}</span>
                            <span class="d-block mb-0 fs-12 text-muted">{{ auth()->user()->role->libelle ?? '' }}</span>
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-mega-sm header-dropdown-menu p-3">
                        <div class="border-bottom pb-2 mb-2 d-flex align-items-center gap-2">
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="Avatar Image" class="avatar-md">
                            <div>
                                <h6 class="mb-0 lh-base">{{ auth()->user()->name ?? 'Utilisateur' }}</h6>
                                <p class="mb-0 fs-13 text-muted">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-1 border-bottom pb-1">
                            <li><a class="dropdown-item" href="{{ route('profil.index') }}"><i class="bi bi-person me-1"></i> Mon profil</a></li>
                        </ul>
                        <ul class="list-unstyled mb-0">
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- END Header -->

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 bg-transparent">
            <div class="d-flex justify-content-between align-items-center bg-body">
                <div class="d-flex align-items-center border-0 px-3">
                    <i class="bi bi-search me-2"></i>
                    <input class="d-flex w-full py-3 bg-transparent border-0 focus-ring" placeholder="Search Here.." autocomplete="off" autocorrect="off" spellcheck="false" aria-autocomplete="list" role="combobox" aria-expanded="true" type="text">
                </div>
                <button type="button" class="btn-close pe-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-body mt-4">
                <p class="font-normal mb-2">Searching For...</p>
                <span class="badge bg-light-subtle border text-body">Analytics <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Project <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Eccomerce <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">CRM <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Logistics <i class="ri-close-line"></i></span>
                <span class="badge bg-light-subtle border text-body">Academy <i class="ri-close-line"></i></span>
            </div>
        </div>
    </div>
</div>
