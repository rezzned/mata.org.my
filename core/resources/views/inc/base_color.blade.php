@php
$color = '#'.$color;
$color1 = '#'.$color1;
$navbar_bg_color = '#'.$navbar_bg_color;

if (!$color or !checkhexcolor($color)) {
  $color = $color;
}

if (!$color1 or !checkhexcolor($color1)) {
  $color1 = "#0a3041";
}
@endphp
<style>
  .sticky-navbar {
    background-color: {{$navbar_bg_color}} !important;
  }
  .support-bar-area, .support-bar-area a, .main-menu li a {
    color: {{$color}};
  }

  .support-bar-area .support-contact-info i {
    color: {{$color}};
  }

  .main-menu li.active a {
    color: {{$color}};
  }

  .main-menu li.active a {
    color: {{$color}};
  }

  .main-menu li a::before {
    background-color: {{$color}};
  }

  .main-menu li a::after {
    background-color: {{$color}};
  }

  .main-menu li a.boxed-btn:hover {
    border: 1px solid {{$color}};
    color: {{$color}};
  }

  a.boxed-btn {
    background-color: {{$color}};
  }

  .main-menu li a.boxed-btn {
    border: 1px solid {{$color}};
  }

  a.hero-boxed-btn:hover {
    background-color: {{$color}};
  }

  .intro-txt {
    background-color: {{$color}};
  }

  a.boxed-btn {
    background-color: {{$color}};
  }

  .approach-summary a.boxed-btn:hover {
    border: 1px solid {{$color}};
    color: {{$color}};
  }

  .single-approach:hover .approach-icon-wrapper {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .approach-icon-wrapper i {
    color: {{$color}};
  }

  a.boxed-btn {
    background-color: {{$color}};
  }

  a.readmore-btn {
    background-color: {{$color}};
  }

  .single-case p {
    color: {{$color}};
  }

  .single-testimonial h6 {
    color: {{$color}};
  }

  .single-testimonial::before {
    border-top: 2px solid {{$color}};
    border-right: 2px solid {{$color}};
  }

  .single-testimonial::after {
    border-bottom: 2px solid {{$color}};
    border-left: 2px solid {{$color}};
  }

  .social-accounts {
    background-color: {{$color}};
  }

  .social-accounts ul li a:hover {
    color: {{$color}};
  }

  .social-accounts ul li a:hover {
    color: {{$color}};
  }

  .single-blog::before {
    border-right: 2px solid {{$color}};
    border-bottom: 2px solid {{$color}};
  }

  .single-blog::after {
    border-top: 2px solid {{$color}};
    border-left: 2px solid {{$color}};
  }

  .blog-txt .date span {
    color: {{$color}};
  }

  .blog-txt .blog-title a:hover {
    color: {{$color}};
  }

  a.readmore-btn {
    background-color: {{$color}};
  }

  ul.footer-links li a::after {
    color: {{$color}};
  }

  ul.footer-links li a:hover {
    color: {{$color}};
  }

  .footer-newsletter button[type="submit"]:hover,
  .footer-newsletter input[type="submit"]:hover {
    color: {{$color}};
  }

  .footer-newsletter button[type="submit"],
  .footer-newsletter input[type="submit"] {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .footer-contact-info ul li i {
    color: {{$color}};
  }

  .back-to-top {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .back-to-top:hover {
    color: {{$color}};
  }

  ul.breadcumb li a:hover {
    color: {{$color}};
  }

  .main-menu li a:hover {
    color: {{$color}};
  }

  .approach-icon-wrapper {
    border: 1px solid {{$color}};
  }

  .case-carousel button.owl-next:hover {
    border: 2px solid {{$color; }} !important;
  }

  .case-carousel button.owl-next:hover i {
    color: {{$color}};
  }

  .member-info small {
    color: {{$color}};
  }

  .single-team-member::before {
    border-top: 2px solid {{$color}};
    border-left: 2px solid {{$color}};
  }

  .single-team-member::after {
    border-bottom: 2px solid {{$color}};
    border-right: 2px solid {{$color}};
  }

  .loader-inner {
    background-color: {{$color}};
  }

  .single-service::before {
    border-right: 2px solid {{$color}};
    border-bottom: 2px solid {{$color}};
  }

  .single-service::after {
    border-top: 2px solid {{$color}};
    border-left: 2px solid {{$color}};
  }

  .pagination-nav li.page-item.active a {
    background-color: {{$color}};
    border: 2px solid {{$color}};
  }

  .category-lists ul li a::after {
    color: {{$color}};
  }

  .category-lists ul li a:hover {
    color: {{$color}};
  }

  .subscribe-section span {
    color: {{$color}};
  }

  .subscribe-section h3::after {
    background-color: {{$color}};
  }

  .subscribe-form input[type="submit"],
  .subscribe-form button[type="submit"] {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .subscribe-form input[type="submit"]:hover,
  .subscribe-form button[type="submit"]:hover {
    border: 1px solid {{$color}};
    color: {{$color}};
  }

  .project-ss-carousel .owl-next {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .project-ss-carousel .owl-next:hover {
    color: {{$color}};
  }

  .project-ss-carousel .owl-prev {
    background-color: {{$color}};
    border: 1px solid {{$color}};
  }

  .project-ss-carousel .owl-prev:hover {
    color: {{$color}};
  }

  .popular-post-txt h5 a:hover {
    color: {{$color}};
  }

  .single-contact-info i {
    color: {{$color}};
  }

  .support-bar-area ul.social-links li a:hover {
    color: {{$color}};
  }

  .main-menu li.dropdown:hover a {
    color: {{$color}};
  }

  .main-menu li.dropdown ul.dropdown-lists li a::before {
    background-color: {{$color}};
  }

  .main-menu li.dropdown ul.dropdown-lists li.active a {
    background-color: {{$color}};
  }

  .main-menu li.dropdown.active::after {
    color: {{$color}};
  }

  .single-category .text a.readmore {
    color: {{$color}};
  }

  .category-lists ul li.active a {
    color: {{$color}};
  }

  .case-types ul li a {
    border: 1px solid {{$color}};
  }

  .case-types ul li a:hover {
    background-color: {{$color}};
  }

  .case-types ul li.active a {
    background-color: {{$color}};
  }

  .main-menu li.dropdown:hover::after {
    color: {{$color}};
  }

  .mega-dropdown .dropbtn::before {
    background-color: {{$color}};
  }

  .mega-dropdown .dropbtn::after {
    background-color: {{$color}};
  }

  .mega-dropdown-content .service-category a::before {
    color: {{$color}};
  }

  .mega-dropdown-content .service-category h3 {
    color: {{$color}};
  }

  .testimonial-carousel.owl-theme .owl-dots .owl-dot.active span {
    background: {{$color}};
  }

  .owl-carousel.common-carousel .owl-nav button.owl-next,
  .owl-carousel.common-carousel .owl-nav button.owl-prev {
    background: {{$color}};
    border: 1px solid {{$color}};
  }

  .owl-carousel.common-carousel .owl-nav button.owl-next:hover,
  .owl-carousel.common-carousel .owl-nav button.owl-prev:hover {
    color: {{$color}};
  }

  .mega-dropdown .service-category a.active {
    color: {{$color}};
  }

  .mega-dropdown .dropbtn.active {
    color: {{$color}};
  }

  .case-types ul li a {
    color: {{$color}};
  }

  .mega-dropdown:hover a.dropbtn {
    color: {{$color}};
  }

  .mega-dropdown .dropbtn::before {
    background-color: {{$color}};
  }

  .mega-dropdown .dropbtn::after {
    background-color: {{$color}};
  }

  .single-pic h4::after {
    background-color: {{$color}};
  }

  .video-play-button:before {
    background: {{$color}};
  }

  .video-play-button:after {
    background: {{$color}};
  }

  .project-ss-carousel.owl-theme .owl-dots .owl-dot.active span {
    background: {{$color}};
  }

  .pagination-nav li.page-item.active a,
  .pagination-nav li.page-item.active span {
    background-color: {{$color}};
    border: 2px solid {{$color}};
  }

  .statistics-section h5 i {
    color: {{$color}};
  }

  .hero2-carousel.owl-theme .owl-dots .owl-dot.active span {
    background-color: {{$color}};
  }

  button.cookie-consent__agree {
    background-color: {{$color}};
  }

  button.mfp-close:hover {
    background-color: {{$color}};
  }

  .single-pricing-table:hover a.pricing-btn {
    background-color: {{$color}};
  }

  .single-pricing-table a.pricing-btn:hover {
    background-color: #fff;
    color: {{$color}};
  }

  .single-pricing-table:hover {
    background-color: {{$color}};
    border: 2px solid {{$color}};
  }

  .single-pricing-table .price {
    color: {{$color}};
  }

  .package-order {
    background-color: {{$color}};
    border-color: {{$color}};
  }

  ul.language-dropdown li a::before {
    background: {{$color}};
  }

  a.language-btn:hover {
    color: {{$color}};
  }

  .single-job a.title {
    color: {{$color}};
  }

  .single-job strong i {
    color: {{$color}};
  }

  .job-details h3 {
    color: {{$color}};
  }

  .service-txt .service-title a:hover {
    color: {{$color}};
  }


  .intro-txt a {
    background-color: {{$color1}};
  }

  .sticky-navbar {
    background-color: {{$color1}};
  }

  .footer-section {
    background-color: {{$color1}};
  }

  .mega-dropdown-content {
    background-color: {{$color1}};
  }

  .main-menu li.dropdown ul.dropdown-lists li {
    background-color: {{$color1}};
  }

  ul.language-dropdown li {
    background-color: {{$color1}};
  }

  input[type="submit"],
  button[type="submit"] {
    background-color: {{$color1}};
    border: 1px solid {{$color1}};
  }

  input[type="submit"]:hover,
  button[type="submit"]:hover {
    color: {{$color1}};
  }

  .subscribe-section {
    background-color: {{$color1}};
  }

  a.hero-boxed-btn::before {
    border-top: 2px solid {{$color1}};
    border-left: 2px solid {{$color1}};
  }

  a.hero-boxed-btn::after {
    border-right: 2px solid {{$color1}};
    border-bottom: 2px solid {{$color1}};
  }

  .fc-button-primary {
    background-color: {{$color1}};
    border-color: {{$color1}};
  }

  .fc-button-primary:hover {
    background-color: {{$color}};
    border-color: {{$color}};
  }

  .fc-button-primary:not(:disabled).fc-button-active,
  .fc-button-primary:not(:disabled):active {
    background-color: {{$color}};
    border-color: {{$color}};
  }

  .services-area .services-item .services-content a {
    color: {{$color}};
  }

  .services-area .services-item .services-content a {
    color: {{$color}};
  }

  .services-area .services-item .services-content a i {
    color: {{$color}};
  }

  .services-area .services-item:hover .services-content a i {
    background: {{$color}};
  }

  .services-area .services-item:hover .services-content .title {
    color: {{$color}};
  }

  ul.slicknav_nav {
    background-color: {{$color1}};
  }

  .slicknav_nav ul.dropdown-lists {
    background-color: {{$color1; }}1a;
  }

  .table .thead-dark th {
    background-color: {{$color1}};
  }

  .header-absolute.no-breadcrumb {
    background-color: {{$color1}};
  }

  .user-dashbord button[type="submit"] {
    background-color: {{$color}};
  }

  .user-dashbord button[type="submit"]:hover {
    color: #fff;
  }

  .single_checkbox input:checked+label,
  .single_radio input:checked+label {
    color: {{$color}};
  }

  .single_checkbox input:checked+label .box:before,
  .single_radio input:checked+label .circle:before {
    background: {{$color}};
  }

  .categories-widget ul li:hover {
    color: {{$color}};
  }

  .price-range-widget .ui-widget .ui-slider-handle {
    background: {{$color}};
  }

  .price-range-widget .ui-slider .ui-slider-range {
    background: {{$color}};
  }

  .course-details-section .discription-area .discription-tabs .nav-tabs .nav-link.active {
    background: {{$color}};
  }

  .mega-tab h3.category a {
    color: {{$color}};
  }

  @media only screen and (max-width: 991px) {
    a.language-btn:hover {
      color: {{$color}};
    }

    .slicknav_nav .slicknav_row:hover {
      background: {{$color}};
    }

    .slicknav_nav a:hover {
      background: {{$color}};
    }

    h5.service-title {
      color: {{$color}};
    }
  }

  @media only screen and (max-width: 575px) {
    .case-types ul li a {
      background-color: #fff;
    }
  }
</style>