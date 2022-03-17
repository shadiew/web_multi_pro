<?php
$content_max_width        = absint( $this->get( 'content_max_width' ) );

$amp_logo        = digiqole_option( 'amp_header_logo' );
$amp_footer_logo = digiqole_option( 'amp_footer_logo' );
$amp_footer_color    = digiqole_option( 'amp_footer_color' );
$amp_footer_bg       = digiqole_option( 'amp_footer_bg' );
$amp_footer_bg       = digiqole_option( 'amp_footer_bg' );
$style_darklight_mode = digiqole_option('style_theme_setting');

if ( empty( $amp_footer_bg ) ) {
	$amp_footer_bg = '#000';
}

if ( empty( $amp_footer_color ) ) {
	$amp_footer_color = '#fff';
} ?>

html {
    background: #fff;
}

body,
.amp-wp-article-content {
	font-size: 14px;
	font-weight: 400;
	font-family: 'Roboto', sans-serif;
	line-height: 22px;
}

body.dark-mode,
.dark-mode .amp-wp-article-content {
    background: #333;
    color: #fff;
}

h1,
h2,
h3,
h4,
h5,
h6,
.h1,
.h2,
.h3,
.h4,
.h5,
.h6,
h1.amp-wp-title {
    font-weight: 700;
    font-family: "Barlow", sans-serif;
    -ms-word-wrap: break-word;
    word-wrap: break-word;
    letter-spacing: 0.45px;
}

.amp-wp-title {
    margin-bottom: 20px;
    font-size: 26px;
    line-height: 30px;
}

.amp-wp-article-content p {
	line-height: 28px;
	margin: 10px 0 12px 0;
}

.wp-block-quote p{
	font-size: 18px;
}

.topbar.topbar-gray {
    background-color: whitesmoke;
}

.topbar.topbar-gray .top-info {
	padding: 5px 0;
	margin: 0;
	text-align: center;
}

.topbar.topbar-gray .top-info li {
    color: #777777;
    font-size: 13px;
    border-right: none;
    padding-right: 0;
}

.header-middle-area {
    padding: 20px 0 10px;
}

.dark-mode .header-middle-area {
    background: #333;
}

.logo-area {
	max-width: 160px;
	margin: 0 auto;
}

.amp-menu-slide .logo-area{
	margin-top: 30px;
	margin-left: 20px;
}

.dark-mode .amp-menu-slide{
    background: #333;
}

.header-gradient {
    background-image: -o-linear-gradient(70deg, #f84270 0%, #fe803b 100%);
    background-image: linear-gradient(20deg, #f84270 0%, #fe803b 100%);
	padding: 15px 0;
}

.header .navbar-light .elementskit-menu-hamburger {
    background: rgba(255,255,255, 0.2);
    padding: 12px 10px;
    margin: 5px 5px 5px 0;
    border: none;
	width: 45px;
	border-radius: 0.25rem;
	position: relative;
	cursor: pointer;
}

.header .navbar-light .elementskit-menu-hamburger:focus {
	border: none;
	outline: none;
}

.header .navbar-light .elementskit-menu-hamburger .elementskit-menu-hamburger-icon {
    background: #fff;
    height: 1px;
    width: 100%;
    display: block;
	margin: 0 0 5px 0;
}

.header .navbar-light .elementskit-menu-hamburger .elementskit-menu-hamburger-icon:last-child{
	margin-bottom: 0;
}

.hamburger {
	display: inline-block;
}

.hamburger:focus {
	border: none;
	outline: none;
}

.amp-menu-slide {
	max-width: 300px;
	right: auto;
	width: 100%;
}

.amp-nav {
	padding: 20px;
}

.amp-nav li {
	list-style: none;
	line-height: 50px;
	text-transform: uppercase;
}

.amp-nav li a {
	font-weight: 700;
}

.header-search-icon .form-control {
    padding: 10px 10px 10px 20px;
    border-radius: 3px;
    border: transparent;
    background: transparent;
    color: #fff;
    max-width: 150px;
    position: relative;
}

.header-search-icon .form-control:focus{
    border: none;
}

.input-group-btn.search-button,
.input-group-btn.search-button:foucs,
.input-group-btn.search-button:hover{
    position: relative;
    background: transparent;
    border: none;
    outline-style: none;
    cursor: pointer;
}

.nav-search-area .input-group{
    position: relative;
    padding-right: 15px;
    border: 1px solid #fff;
    border-radius: 4px;
}

.input-group-btn.search-button{
    border: none;
    outline-style: none;
    background: transparent;
    position: absolute;
    right: 5px;
    bottom: 0;
    top: 0;
    cursor: pointer;
    margin: auto;
}

.comment-info .form-control,
.blog-post-comment .form-control  {
	display: block;
    width: 94%;
    padding: .375rem 0 .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
	transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
	border-radius: 0;
    height: 45px;
    margin-bottom: 15px;
}

.blog-post-comment textarea.form-control {
	height: 245px;
}

.blog-post-comment .btn {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    background: #fc4a00;
    height: 45px;
    padding: 0 35px;
    line-height: 42px;
    border-radius: 0px;
    -webkit-border-radius: 0px;
    -ms-border-radius: 0px;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    outline: none;
    text-decoration: none;
    cursor: pointer;
    border: none;
}

.blog-post-comment .btn:hover {
    background: #e74907;
    border-color: #e74907;
}

.comment-info .col-md-6,
.comment-info .col-md-12,
.blog-post-comment .col-md-12 {
	width: 100%;
	max-width: 850px;
}

.blog-post-comment .comment-respond .comment-form .comment-form-cookies-consent {
	display: none;
}

.comment-info .form-control:focus,
.blog-post-comment .form-control:focus{
	border: 1px solid #fc4a00;
}

.header-search-icon .form-control::placeholder,
.newsletter-area .footer-newsletter input[type=email]::placeholder{
	color: #fff;
}

.newsletter-area .footer-newsletter i.fa {
    position: absolute;
    left: 15px;
    bottom: 15px;
    color: #fff;
    font-size: 17px;
}

.form-control:focus{
	outline: none;
	border: 1px solid #fff;
}

.amp-wp-article-footer .amp-wp-meta.amp-wp-tax-category{
	display: none;
}

.nav-search-area {
	float: right;
    margin: 5px 0 5px 5px;
}

.amp-wp-enforced-sizes {
    max-width: 100%;
    margin: 0 auto;
}

.amp-wp-unknown-size img {
    object-fit: contain;
}

.amp-wp-article-header {
    display: block;
    overflow: hidden;
    margin-bottom: 0;
}

.amp-wp-article-header .amp-wp-meta:first-of-type {
    font-weight: 700;
    float: left;
}

.amp-wp-meta.amp-wp-posted-on {
    float: right;
}

.amp-wp-article-content:before,
.amp-wp-article-content:after {
    clear: both;
    display: table;
    content: '';
}

.amp-wp-article-footer {
    display: block;
    clear: both;
    overflow: hidden;
    position: relative;
}

.blog-single .tag-lists a {
    border-color: #aaaaaa;
    color: #aaaaaa;
}

.amp-wp-article-footer .amp-wp-tax-category,
.amp-wp-article-footer .amp-wp-tax-tag{
	font-size: 14px;
}

.amp-wp-tax-category a,
.amp-wp-tax-tag a{
	border: 1px solid #aaaaaa;
	color: #aaaaaa;
	padding: 4px 13px;
	font-size: 14px;
	border-radius: 30px;
	line-height: 32px;
}

.dark-mode .amp-wp-tax-category a,
.dark-mode .amp-wp-tax-tag a{
    color: #fff;
    border: 1px solid #fff;
}

.amp-wp-header a {
    color: #333;
    text-decoration: none;
}

h1,
.h1 {
	font-size: 30px;
    line-height: 36px;
}

h2,
.h2 {
	font-size: 26px;
    line-height: 32px;
}

h3,
.h3 {
	font-size: 24px;
    line-height: 28px;
}

h4,
.h4 {
	font-size: 18px;
    line-height: 22px;
}

.h5,
h5 {
	font-size: 16px;
    line-height: 20px;
}

h6,
.h6 {
	font-size: 14px;
    line-height: 28px;
}

a,
.entry-header .entry-title a:hover,
.sidebar ul li a:hover {
    color: #fc4a00;
    transition: all ease 500ms;
}

figure.wp-block-image.size-large {
	margin: 0;
}

.elementor-section {
    padding: 30px 0;
}

.amp-wp-title {
    display: none;
}

.single-post-header .amp-wp-title {
    display: block;
}

.container {
    width: 100%;
    margin-right: auto;
    margin-left: auto;
	max-width: 860px;
}

.amp-wp-article,
.header-wrapper,
.amp-footer-container {
    max-width: 860px;
    padding-left: 15px;
    padding-right: 15px;
    margin-left: auto;
    margin-right: auto;
    overflow-wrap: break-word;
    word-wrap: break-word;
}

.amp-wp-article {
	margin-bottom: 40px;
}

.amp-wp-article-content {
    position: relative;
    display: block;
    margin-top: 0;
    clear: both;
    margin-bottom: 0;
}

.menu-close {
	top: 5px;
    right: 5px;
	position: absolute;
	color: #333;
	font-size: 30px;
	cursor: pointer;
}

.menu-close:focus {
	border: none;
	outline: none;
}

.entry.amp-wp-article-content-width {
    margin-right: auto;
    margin-left: auto;
}

.amp-wp-article-content dt {
    margin-bottom: 10px;
}

.amp-wp-article-featured-image {
    display: block;
    margin: 0;
    position: relative;
}

.amp-wp-article-featured-image img {
	border-radius: 5px;
}

.amp-wp-article-content p,
.amp-wp-article-content ul,
.amp-wp-article-content dd {
    margin-bottom: 1.5rem;
}

.amp-wp-article-content li > ul,
.amp-wp-article-content li > ol {
    margin-top: 10px;
    margin-bottom: 0;
}

.amp-wp-article-content dt {
    font-weight: 700;
}

.breadcrumb {
    background: transparent;
    padding: 25px 0 17px;
    margin: 0 0 40px 0;
	display: flex;
	flex-wrap: wrap;
	list-style: none;
}

.breadcrumb li {
    padding-right: 10px;
    color: #777777;
    font-size: 14px;
    font-weight: 400;
    line-height: 22px;
	list-style: none;
}

.dark-mode .breadcrumb li{
    color: #fff;
} 

.breadcrumb li a {
    padding: 0 9px;
	color: #fc4a00;
}

.amp-wp-article-content p a:not(button),
.comment-content a {
    color: #ff8763;
    -webkit-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -moz-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -ms-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -o-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    text-decoration-line: underline;
    text-decoration-color: transparent;
    -webkit-text-decoration-color: transparent;
}

.amp-wp-article-content p a:not(button):hover,
.amp-wp-article-content p a:not(button):focus,
.comment-content a:hover,
.comment-content a:focus {
    text-decoration: underline;
    text-decoration-color: currentColor;
    -webkit-text-decoration-color: currentColor;
}

.amp-wp-article-content img,
.amp-wp-article-content video {
    max-width: 100%;
    height: auto;
}

.amp-wp-article-content address {
    margin-bottom: 30px;
}

.aligncenter {
    display: block;
    clear: both;
    margin-right: auto;
    margin-left: auto;
}

.alignleft {
    display: inline;
    float: left;
    margin-right: 30px;
    margin-left: 0;
    max-width: calc(5 * (100vw / 12));
}

.alignright {
    display: inline;
    float: right;
    margin-left: 30px;
    max-width: 600px;
}

.alignnone {
    margin-left: auto;
    margin-right: auto;
    max-width: 100%;
}

.wp-caption img[class*="wp-image-"] {
    display: block;
    margin-right: auto;
    margin-left: auto;
}

.amp-wp-article-content figure a {
    border-bottom: none;
}

.amp-wp-article-content .fluid-width-video-wrapper {
    margin-top: 30px;
    margin-bottom: 30px;
}

.amp-wp-article-content iframe {
    overflow: hidden;
    margin-right: auto;
    margin-bottom: 30px;
    margin-left: auto;
    max-width: 100%;
}

.amp-wp-article-content h1, .amp-wp-article-content h2 {
    margin-top: 1em;
    margin-bottom: .5em;
}

.amp-wp-article-content h3,
.amp-wp-article-content h4,
.amp-wp-article-content h5 {
    margin-top: 1em;
    margin-bottom: .5em;
}

.amp-wp-article-content h6 {
    margin-top: 1em;
    margin-bottom: .75em;
}

.amp-wp-article-content ol {
    clear: both;
    margin-bottom: 1.25rem;
    margin-left: 1.25rem;
    list-style-position: inside;
    list-style-type: decimal;
}

.amp-wp-article-content ul {
    clear: both;
    margin-bottom: 1.25rem;
    margin-left: 1.25rem;
    list-style: circle;
}

.amp-wp-article-content li {
    position: relative;
    margin-bottom: 10px;
}

.wp-caption-text, .image-caption {
    color: #555;
    font-size: .825rem;
    font-family: 'Montserrat', sans-serif;
    line-height: 1.5;
}

.wp-caption-text {
    margin-top: 10px;
    text-align: right;
}

.wp-caption .wp-caption-text {
    margin-bottom: 20px;
}

.gallery {
    position: relative;
    display: block;
    overflow: hidden;
    margin: -5px;
}

.gallery-item {
    position: relative;
    display: block;
    float: left;
    margin: 0;
    padding: 5px;
}

.gallery-item img {
    display: block;
    width: 100%;
    height: auto;
}

.gallery-item div {
    margin: 0;
}

.gallery-item .wp-caption-text, .gallery-caption {
    position: absolute;
    top: auto;
    right: 1px;
    bottom: 0;
    left: 1px;
    padding: 5px 10px;
    background-color: rgba(0, 0, 0, .7);
    color: #fff;
    line-height: 1.5;
}

.amp-wp-article-content .twitter-tweet {
    margin-right: auto;
    margin-bottom: 30px;
    margin-left: auto;
}

.amp-wp-article-content iframe.instagram-media {
    margin-right: auto;
    margin-bottom: 1.5em;
    margin-left: auto;
}

.gallery-columns-1 .gallery-item {
    width: 100%;
}

.gallery-columns-2 .gallery-item {
    width: 50%;
}

.gallery-columns-2 .gallery-item:nth-child(2n +1) {
    clear: both;
}

.gallery-columns-3 .gallery-item {
    width: 33.33%;
}

.gallery-columns-3 .gallery-item:nth-child(3n +1) {
    clear: both;
}

.gallery-columns-4 .gallery-item {
    width: 25%;
}

.gallery-columns-4 .gallery-item:nth-child(4n +1) {
    clear: both;
}

.gallery-columns-5 .gallery-item {
    width: 20%;
}

.gallery-columns-5 .gallery-item:nth-child(5n +1) {
    clear: both;
}

.gallery-columns-6 .gallery-item {
    width: 16.66%;
}

.gallery-columns-6 .gallery-item:nth-child(6n +1) {
    clear: both;
}

.gallery-columns-7 .gallery-item {
    width: 14.285%;
}

.gallery-columns-7 .gallery-item:nth-child(7n +1) {
    clear: both;
}

.gallery-columns-8 .gallery-item {
    width: 12.5%;
}

.gallery-columns-8 .gallery-item:nth-child(8n +1) {
    clear: both;
}

.gallery-columns-9 .gallery-item {
    width: 11.111%;
}

.gallery-columns-9 .gallery-item:nth-child(9n +1) {
    clear: both;
}

.amp-wp-article-content .list-post,
.amp-wp-article-content .post-block-list .list-post {
    padding: 0;
    margin: 0;
}

.amp-wp-article-content h3ujccccccv.post-title {
    margin-top: 0;
}

.list-post li {
    list-style: none;
}

/* Gutenberg */
ul.wp-block-gallery {
    margin-bottom: 1.5rem;
    margin-left: 0;
}

.wp-block-separator {
    position: relative;
    display: block;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    padding-top: 2px;
    border: none;
    background-image: linear-gradient(to right, rgba(0, 0, 0, .1) 66.666%, rgba(255, 255, 255, 0) 0%);
    background-position: top;
    background-size: 20px 1px;
    background-repeat: repeat-x;
}

.entry-content a.wp-block-button__link,
a.wp-block-button__link {
    color: #fff;
    font-size: 1rem;
    margin-bottom: 20px;
    -webkit-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -moz-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -ms-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -o-transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    transition: all .3s cubic-bezier(0.32, 0.74, 0.57, 1);
}

body .entry-content a.wp-block-button__link,
body a.wp-block-button__link {
    text-decoration: none;
}

.entry-content a.wp-block-button__link:hover {
    background-color: #ff8763;
}

.wp-block-quote, blockquote {
	position: relative;
	margin-top: 40px;
	margin-bottom: 30px;
	text-align: center;
	border: none;
	padding: 30px;
	font-style: italic;
}

blockquote cite,
.wp-block-quote cite {
    font-style: italic;
    font-weight: 700;
    color: #333333;
    font-size: 16px;
}

.dark-mode blockquote cite,
.dark-mode .wp-block-quote cite{
    color: #fff;
}

blockquote cite:before{
	position: absolute;
	top: 0;
	width: 30px;
	height: 1px;
	background: #6c757d;
	content: '';
	bottom: 0;
	margin: 10px 0 13px -40px;
}

.dark-mode blockquote cite:before,
.dark-mode blockquote cite::after{
    background: #fff;
}

blockquote cite::after {
    position: absolute;
    top: 0;
    width: 30px;
    height: 1px;
    background: #6c757d;
    content: "";
    bottom: 0;
    margin: 10px -40px 13px 0px;
    right: 0;
}

.wp-block-quote:before,
blockquote:before {
    content: '\f10d';
    font-family: 'Fontawesome';
    font-size: 30px;
	color: #222222;
	font-style: normal;
}

.digiqole-serach i {
    color: #fff;
}

.amp-post-carousel {
    max-width: 100%;
    margin: 0 20px 20px 0;
    height: 400px;
}

.elementor-column.elementor-col-50 .amp-post-carousel {
    margin-right: 20px;
}

.elementor-column.elementor-col-100 .amp-post-carousel {
    margin-right: 0;
}

.amp-post-carousel.grid-slider {
    margin: 0 0 20px 0;
}

.amp-post-carousel.grid-slider .item.item-before {
    padding-bottom: 100%;
}

.img-link {
    text-indent: -999999px;
    visibility: hidden;
}

.dark-mode .wp-block-quote:before,
.dark-mode blockquote:before {
	color: #fff;
}

.ts-footer .footer-info li i.fa-home {
    color: #f45946;
}

.ts-footer .footer-info li i.fa-envelope {
    color: #04aea0;
}

.ts-footer .recent-posts-widget .post-content .post-meta span {
    color: #999999;
    font-weight: 400;
}

.ts-footer .recent-posts-widget .post-content .post-tag .post-cat{
    display: none;
}

.ts-footer .recent-posts-widget .post-content .post-meta{
    margin-bottom: 15px;
}

.ts-footer .recent-posts-widget .post-content .post-title{
    color: #fff;
}

.wp-block-quote p,
blockquote p {
    font-weight: 700;
}

.wp-block-cover__inner-container p {
    color: #fff;
}

.wp-block-pullquote {
    margin-top: 0;
    margin-bottom: 1.5rem;
    padding: 20px;
}

.copyright-text {
	display: inline-block;
    width: 83%;
}

.top-up-btn{
	display: inline-block;
    width: 15%;
}

.top-up-btn a {
    background: #fc4a00 none repeat scroll 0 0;
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    height: 44px;
    position: relative;
    text-align: center;
    width: 44px;
    display: block;
    padding: 0;
    -webkit-transition: all 0.3s;
    -o-transition: all 0.3s;
    transition: all 0.3s;
	margin-left: auto;
	line-height: 40px;
}

.wp-block-table.is-style-stripes {
    border-right: 1px solid rgba(0, 0, 0, .05);
    border-bottom: 1px solid rgba(0, 0, 0, .05);
    border-left: 1px solid rgba(0, 0, 0, .05);
}

.wp-block-archives-dropdown {
    margin-bottom: 30px;
}

.wp-block-archives-dropdown select {
    min-width: 300px;
}

.amp-wp-tax-category a,
.amp-wp-tax-tag a {
    color: #333;
}

.wp-block-quote p, blockquote p {
    margin-top: 0;
}

.btn-toggle {
    position: absolute;
    top: auto;
    bottom: auto;
    left: 0;
    display: block;
    overflow: hidden;
    width: 40px;
    height: 50px;
    cursor: pointer;
}

.btn-toggle .off-canvas-toggle {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 20;
    display: block;
    width: 100%;
    height: 100%;
    -webkit-transition: opacity .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -moz-transition: opacity .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -ms-transition: opacity .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -o-transition: opacity .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    transition: opacity .3s cubic-bezier(0.32, 0.74, 0.57, 1);
}

.icon-toggle {
    position: absolute;
    top: 50%;
    left: 0;
    display: block;
    width: 100%;
    height: 1px;
    background-color: currentColor;
    font-size: 0;
    -webkit-transition: background-color .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -moz-transition: background-color .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -ms-transition: background-color .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -o-transition: background-color .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    transition: background-color .3s cubic-bezier(0.32, 0.74, 0.57, 1);
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.icon-toggle:before,
.icon-toggle:after {
    position: absolute;
    left: 0;
    width: 75%;
    height: 100%;
    background-color: currentColor;
    content: '';
    -webkit-transition: -webkit-transform 0.35s, width .2s cubic-bezier(0.32, 0.74, 0.57, 1);
    -moz-transition: -moz-transform 0.35s, width .2s cubic-bezier(0.32, 0.74, 0.57, 1);
    -ms-transition: -ms-transform 0.35s, width .2s cubic-bezier(0.32, 0.74, 0.57, 1);
    transition: transform 0.35s, width .2s cubic-bezier(0.32, 0.74, 0.57, 1);
}

.icon-toggle:before {
    -webkit-transform: translateY(-700%);
    transform: translateY(-700%);
}

.icon-toggle:after {
    -webkit-transform: translateY(800%);
    transform: translateY(800%);
}

#sidebar-left {
    background-color: #333;
    color: #fff;
    padding: 40px 20px;
}

#sidebar-left ul {
    list-style: none;
    padding: 0;
}

#sidebar-left a {
    color: #fff;
    text-decoration: none;
    display: block;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    min-width: 240px;
    line-height: 40px;
    font-size: 0.875rem;
    text-transform: uppercase;
}

.amp-wp-footer {
    background-color: #000;
    color: #fff;
    margin: 0;
    padding: 15px 0;
    text-align: center;
}

.footer-logo {
	max-width: 220px;
}

.newsletter-area .footer-newsletter {
    position: relative;
}

.mc4wp-form-fields {
	overflow: hidden;
}

.newsletter-area .footer-newsletter input[type=email] {
    background: transparent;
    height: 48px;
    color: #fff;
    border: none;
    width: 100%;
    padding: 0 0 0 40px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -ms-border-radius: 5px;
}

.newsletter-area .footer-newsletter input[type=email]:focus {
	outline: none;
	border: none;
}

.newsletter-area .footer-newsletter input[type=submit] {
    position: absolute;
    right: 0;
    top: 0;
    padding: 0 40px;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -ms-border-radius: 5px;
    height: 48px;
    line-height: 48px;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    background: #222222;
    border: none;
    color: #fff;
    cursor: pointer;
    -o-transition: all 0.4s ease;
    transition: all 0.4s ease;
    -webkit-transition: all 0.4s ease;
    -moz-transition: all 0.4s ease;
    -ms-transition: all 0.4s ease;
    border-bottom-left-radius: 0;
    border-top-left-radius: 0;
}

.newsletter-area .footer-newsletter input[type=submit]:focus{
	outline: none;
	border: none;
}

.amp-wp-article-footer .amp-wp-meta {
    display: block;
    margin: 0 0 15px 0;
}

.amp-wp-tax-category,
.amp-wp-tax-tag {
    font-size: .875em;
    line-height: 1.5em;
    margin: 1.5em 16px;
}

.amp-wp-comments-link {
    font-size: .875em;
    line-height: 1.5em;
    text-align: center;
    margin: 2.25em 0 1.5em;
}

.amp-wp-comments-link a {
    color: #fff;
    background: #fc4a00;
    text-transform: uppercase;
    padding: 13px 45px;
    display: block;
    font-size: 14px;
    font-weight: 600;
    line-height: 18px;
    margin: 0 auto;
    max-width: 200px;
    text-decoration: none;
    width: 50%;
}

.amp-wp-comments-link a:hover {
    background-color: #333;
}

#menu-footer a {
    color: #fff;
    text-decoration: none;
    opacity: 1;
    font-size: 0.875rem;
    text-transform: uppercase;
}

.newsletter-area {
    background-image: -o-linear-gradient(70deg, #f84270 0%, #fe803b 100%);
    background-image: linear-gradient(20deg, #f84270 0%, #fe803b 100%);
    padding: 40px 15px;
}

.post-meta {
    margin-bottom: 25px;
    padding-left: 0;
}

.post-meta li {
    display: inline-block;
	margin-right: 20px;
	vertical-align: middle;
}

.post-meta li.meta-post-view {
	display: none;
}

.post-meta li a.post-cat {
    position: relative;
    left: 0px;
    top: 0px;
    background: #fc4a00;
    color: #fff;
    padding: 0px 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
    line-height: 20px;
    text-transform: uppercase;
    margin-bottom: 7px;
    z-index: 1;
    margin-right: 5px;
    height: 19px;
    border-radius: 4px;
    -webkit-border-radius: 4px;
    -ms-border-radius: 4px;
    letter-spacing: 0.44px;
}

.post-meta li.post-author img {
    position: relative;
    left: 0;
    top: 0;
    width: 45px;
    height: 45px;
    margin-right: 6px;
    display: inline-block;
    border-radius: 50%;
}

.post-meta li.post-author a{
	position: relative;
	top: -15px;
	text-transform: capitalize;
    font-weight: bold;
}

.ts-footer {
    background-color: #222222;
    background-repeat: no-repeat;
	background-size: cover;
	padding: 70px 15px 20px 15px;
	color: #fff;
}

.footer-widget {
	margin-bottom: 30px;
}

.ts-footer .widget-title {
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    line-height: 28px;
    padding-left: 0px;
    position: relative;
    margin-bottom: 40px;
}

.ts-footer .widget-title span {
    background: #222222;
    padding-right: 40px;
    position: relative;
}

.ts-footer .widget-title:before {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 1px;
    bottom: 0;
    margin: auto;
    content: "";
    background: #504f4f;
}

.ts-footer .footer-info {
    padding: 0;
    margin-bottom: 40px;
}

.ts-footer .footer-info li {
	list-style: none;
    line-height: 28px;
}

.breadcrumb li i {
	font-style: normal;
}

.dark-mode .breadcrumb li i{
    color: #fff;
}

.breadcrumb li:last-child i {
	margin-right: 10px;
}

.post-meta li i {
    margin-right: 6px;
    font-size: 15px;
}

.post-meta  .social-share {
	display: none;
}

.ts-footer .footer-info li i {
    margin-right: 15px;
    font-size: 18px;
}

.footer-social {
	display: none;
}

.recent-posts-widget .post-thumb,
.post-thumb-bg .post-thumb {
    -webkit-box-flex: 0;
    -ms-flex: 0 0 38%;
    flex: 0 0 38%;
    width: 38%;
    display: block;
    -webkit-box-ordinal-group: 1;
    -ms-flex-order: 0;
	order: 0;
	position: relative;
	max-width: 225px;
}

.social-share {
	display: none;
}

.post-tab-list .post-title {
	margin: 0 0 4px 0;
}

.recent-posts-widget .post-thumb a,
.post-thumb-bg .post-thumb a {
    display: block;
    border-radius: 4px;
    -webkit-border-radius: 4px;
    -ms-border-radius: 4px;
	overflow: hidden;
	min-height: 140px;
}

.recent-posts-widget .post-thumb .digiqole-sm-bg-img,
.post-thumb-bg .post-thumb .digiqole-sm-bg-img {
    width: 100%;
    height: 100%;
    position: absolute;
    background-size: cover;
    display: block;
    -webkit-transition: all ease 0.5s;
    -o-transition: all ease 0.5s;
    transition: all ease 0.5s;
    border-radius: 4px;
}

.media {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: start;
    -ms-flex-align: start;
	align-items: flex-start;
	margin-bottom: 30px;
}

.post-list-item .post-content .media-body {
    padding-left: 20px;
    -ms-flex-item-align: center;
    -ms-grid-row-align: center;
	align-self: center;
	-webkit-box-flex: 1;
    -ms-flex: 1;
    flex: 1;
}

.related {
    position: relative;
    display: block;
    overflow: hidden;
    margin: 2rem 0;
    padding: 15px 20px 5px 20px;
    border: 1px solid rgba(0, 0, 0, 0.07);
}

.related.is-dark-style {
    display: block;
    border: none;
    background-color: #333;
    color: #fff;
}

.row {
	-ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
}

.related-el {
    display: flex;
    display: -webkit-flex;
    flex-flow: row nowrap;
    flex-grow: 1;
    align-items: center;
}

@media (min-width: 768px) {
    .rb-col-t6 {
        max-width: 50%;
        flex: 0 0 50%;
    }
}

a {
    background-color: transparent;
    color: inherit;
    text-decoration: none;
}

.rb-related-el a:hover, .rb-related-el a:focus {
    text-decoration: underline;
    text-decoration-color: currentColor;
    -webkit-text-decoration-color: currentColor;
}

.rb-related-content > * {
    margin-bottom: 20px;
    display: flex;
    display: -webkit-flex;
    min-width: 0;
    flex-flow: row wrap;
}

.rb-related-el .p-thumb img {
    display: flex;
    display: -webkit-flex;
    width: 90px;
    flex-shrink: 0;
}

.rb-related-el .entry-title {
    display: flex;
    display: -webkit-flex;
    margin: 0;
    padding-left: 15px;
    flex: 1;
}

.rb-related .rb-related-header {
    position: relative;
    display: block;
    margin-top: 0;
    margin-bottom: 20px;
    padding-left: 20px;
}

.rb-related .rb-related-header:before {
    position: absolute;
    top: -5%;
    right: auto;
    bottom: -5%;
    left: 0;
    z-index: 1;
    display: block;
    width: 40px;
    height: 110%;
    background-color: transparent;
    background-image: radial-gradient(currentColor 1px, transparent 1px);
    background-position: 1px 1px;
    background-size: 5px 5px;
    content: '';
    opacity: .25;
}

.related-el figure.p-thumb {
    margin: 0;
}

.amp-wp-content,
.amp-wp-title-bar div {
<?php if ( $content_max_width > 0 ) : ?>
	margin: 0 auto;
	max-width: <?php echo sprintf( '%dpx', $content_max_width ); ?>;
<?php endif; ?>
}

.amp-wp-header {
    text-algin: center;
    -webkit-box-shadow: 0 10px 16px 0 rgba(28, 28, 28, 0.04);
    -moz-box-shadow: 0 10px 16px 0 rgba(28, 28, 28, 0.04);
    box-shadow: 0 10px 16px 0 rgba(28, 28, 28, 0.04);
}

.amp-wp-header div {
    text-align: center;
    font-size: 1em;
    font-weight: 400;
    margin: 0 auto;
    max-width: calc(840px - 32px);
    padding: .875em 16px;
    position: relative;
}


.amp-wp-article-content amp-carousel amp-img,
.amp-wp-article-content figure amp-img{
    margin: 0 auto;
}

<?php if ( !empty($amp_logo['url']) ): ?>
	.amp-wp-header a {
        display: block;
        background-size: contain;
        background-position: center center;
        background-image: url( '<?php echo esc_url( $amp_logo['url'] ); ?>' );
        background-repeat: no-repeat;
        height: 40px;
        width: 300px;
        margin: 0 auto;
        text-indent: -9999px;
	}
<?php else: ?>
	.amp-wp-header a {
        display: inline-block;
        text-decoration: none;
        text-transform: uppercase;
        margin: auto;
        letter-spacing: -0.025em;
        font-weight: 900;
        font-size: 38px;
        line-height: 1;
	}
<?php endif; ?>


/* Site Icon */
.amp-wp-header .amp-wp-site-icon {
    border-radius: 50%;
    position: absolute;
    right: 18px;
    top: 10px;
}

.amp-wp-article-header .amp-wp-meta:last-of-type {
    text-align: right;
}

.amp-wp-byline amp-img,
.amp-wp-byline .amp-wp-author {
    display: inline-block;
    vertical-align: middle;
}

.amp-wp-byline amp-img {
    border-radius: 50%;
    position: relative;
    margin-right: 6px;
}

.amp-wp-posted-on {
    text-align: right;
}

/* Featured image */

.amp-wp-article-featured-image amp-img {
    margin: 0 auto;
}

.amp-wp-article-featured-image.wp-caption .wp-caption-text {
    margin: 0 18px;
}

amp-carousel {
    background: rgba(0,0,0,.07);
    margin: 0 -16px 1.5em;
}

amp-iframe,
amp-youtube,
amp-instagram,
amp-vine {
    background: rgba(0,0,0,.07);
    margin: 0 -16px 1.5em;
}

.amp-wp-article-content amp-carousel amp-img {
    border: none;
}

amp-carousel > amp-img > img {
    object-fit: contain;
}

.amp-wp-footer {
    background-color: <?php echo sanitize_hex_color( $amp_footer_bg ); ?>;
    color: <?php echo sanitize_hex_color( $amp_footer_color ); ?>;
}

.amp-wp-footer h2 {
    display: block;
    text-align: center;
    line-height: 1.375em;
    margin: 0 0 .5em;
}

.amp-wp-footer p {
    font-size: 14px;
    line-height: 22px;
    margin: 0;
}

.amp-wp-footer p {
    text-align: center;
    margin: 20px auto 0 auto;
    max-width: calc(840px - 32px);
}

.amp-wp-footer li {
    display: inline-block;
    list-style: none;
    font-size: 12px;
    padding: 0 10px;
    font-weight: 400;
    text-transform: uppercase;
}

.amp-wp-footer .menu {
    padding: 0;
    text-align: center;
}

a.post-cat {
    position: relative;
    left: 0px;
    top: 0px;
    color: #fff;
    padding: 0px 10px;
    font-size: 11px;
    font-weight: 700;
    display: inline-block;
    line-height: 20px;
    text-transform: uppercase;
    margin-bottom: 7px;
    z-index: 1;
    margin-right: 5px;
    height: 19px;
    border-radius: 4px;
    letter-spacing: 0.44px;
}

.grid-item .ts-overlay-style.featured-post .item{
    min-height: 510px;
    background-size: cover;
    margin-bottom: 20px;
}

.ts-overlay-style {
    position: relative;
    max-height: 100%;
}

.ts-overlay-style::before {
    position: absolute;
    content: "";
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    border-radius: 5px;
    background: linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.8) 100%);
    transition: all 0.4s ease; 
}

.ts-overlay-style:hover::before {
    background-color: rgba(0, 0, 0, 0.2);
}

.ts-overlay-style .post-content {
    padding: 20px 20px 18px;
    position: absolute;
    bottom: 0;
    z-index: 1;
    transition: all 0.4s ease;   
}

.ts-overlay-style:hover .post-content {
    bottom: 10px;
}

.amp-wp-article-content .post-meta-info{
    margin: 0;
}

.post-meta-info {
    display: flex;
    margin: 0;
    padding: 0;
    align-items: center;
}

.ts-overlay-style .post-meta-info li {
    font-size: 13px;
    display: inline-block;
    color: #fff;
    font-weight: 400;
    margin-right: 24px;
}

.ts-overlay-style .post-meta-info li i {
    margin-right: 6px;
    font-size: 13px;
}

.ts-overlay-style .post-content .post-title {
    color: #fff;
    margin: 10px 0 20px;
}

.block-item-post.style1 {
    width: 100%;
    padding: 0;
}

.block-item-post .row {
    display: flex;
    margin-bottom: 20px;
}

.row .col-md-6 {
    width: 50%;
}

.align-items-center {
    align-items: center;
}

.block-item-post .post-thumb-bg .post-thumb {
    width: 100%;
    max-width: 100%;
}

.block-item-post .post-thumb-bg .post-thumb a {
    min-height: 400px;
    display: inline-block;
}

.block-item-post .post-thumb-bg .post-thumb a > span {
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    position: absolute;
    background-size: cover;
    display: block;
    transition: all ease .5s;
    border-radius: 4px;
}

.post-title {
    font-size: 18px;
    font-weight: 600;
    color: #222222;
    line-height: 24px;
}

.post-title.md {
    font-size: 24px;
    font-weight: 700;
    line-height: 30px
}

.block-item-post .post-content.feature-contents {
    padding: 30px;
}

.post-list{
    display: flex;
}

.post-list .col-md-6{
    width: 50%
}

.post-content.media-body {
    padding: 0 15px;
}

.post-list .post-block-style.post-float.media{
    display: flex;
}

.post-list .post-block-style .post-thumb > a {
    min-height: 120px;
    display: inline-block;
}

.post-list .post-block-style .post-content .post-title {
    margin: 0px 0 15px;
}

.post-list .post-cat {
    padding: 0;
}

.post-title:hover a {
    color: #fc4a00;
}

.post-meta span {
    margin-right: 10px;
}

.post-readmore {
    text-transform: uppercase;
    font-weight: 600;
}

.post-readmore:hover {
    color: #fc4a00;
}

.post-block-style:hover .post-thumb a > span {
    background-position: 45%;
}

.digiqole-main-slider .post-slide-item {
    padding-bottom: 115%;
}

.digiqole-main-slider .post-content {
    position: absolute;
    bottom: 0;
    z-index: 1;
    padding: 30px 40px 20px 30px;
}

.digiqole-main-slider .post-title a {
    color: #fff;
}

.digiqole-main-slider .post-slide-item:before {
  position: absolute;
  content: "";
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -ms-border-radius: 5px;
  background: -webkit-gradient(linear, left top, left bottom, color-stop(50%, transparent), to(rgba(0, 0, 0, 0.8)));
  background: -o-linear-gradient(top, transparent 50%, rgba(0, 0, 0, 0.8) 100%);
  background: linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.8) 100%);
  -o-transition: all 0.4s ease;
  transition: all 0.4s ease;
  -webkit-transition: all 0.4s ease;
  -moz-transition: all 0.4s ease;
  -ms-transition: all 0.4s ease;
}

.post-meta-info {
    padding: 0;
    margin: 0;
}

.digiqole-main-slider .post-meta-info li {
    font-size: 13px;
    display: inline-block;
    color: #fff;
    font-weight: 400;
    margin-right: 24px;
}

.post-meta-info li {
    list-style: none;
}

.amp-carousel-button {
    height: 30px;
    width: 30px;
    margin: 5px;
}

.load-more-btn {
    display: none;
}

.ts-category-list-item .ts-category-list {
    padding: 0;
    margin: 0;
}

.ts-category-list-item .ts-category-list li {
    list-style: none;
}

.ts-category-list-item .ts-category-list li a {
    position: relative;
    width: 100%;
    padding: 20px;
    background-size: cover;
    background-position: center;
    color: #fff;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    font-weight: 600;
    font-size: 16px;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
    border-radius: 5px;
    -webkit-border-radius: 5px;
    -ms-border-radius: 5px;
    overflow: hidden;
    min-height: 80px;
}

.ts-category-list-item .ts-category-list li a span {
    position: relative;
    white-space: nowrap;
}

.ts-category-list-item .ts-category-list li a span.bar {
    border-bottom: 1px dashed rgba(255, 255, 255, 0.5);
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
    width: 100%;
    margin: 0 65px 0 25px;
}

.ts-category-list-item .ts-category-list li a .category-count {
    position: absolute;
    background: #fff;
    color: #000;
    padding: 5px;
    width: 40px;
    right: 20px;
    top: 44px;
    margin-top: 12px;
    border-radius: 50%;
    display: inline-block;
    height: 40px;
    text-align: center;
    line-height: 32px;
    top: 0px;
    bottom: 0;
    margin: auto;
}

.heading-style3 .block-title,
.heading-style3 .widget-title,
.sidebar .widget .block-title,
.sidebar .widget .widget-title {
    letter-spacing: 0.64px;
    color: #fc4a00;
    line-height: 25px;
    font-weight: 800;
    font-size: 16px;
    text-transform: uppercase;
    margin: 0;
}

.heading-style3 .block-title .title-angle-shap,
.heading-style3 .widget-title .title-angle-shap,
.sidebar .widget .block-title .title-angle-shap,
.sidebar .widget .widget-title .title-angle-shap {
    display: inline-block;
    padding: 0 0 0 15px;
    position: relative;
}

.heading-style3 .block-title .title-angle-shap:before,
.heading-style3 .widget-title .title-angle-shap:before,
.sidebar .widget .block-title .title-angle-shap:before,
.sidebar .widget .widget-title .title-angle-shap:before {
    width: 3px;
    height: 12px;
    position: absolute;
    top: 0;
    content: "";
    background: #fc4a00;
    left: 0;
}

.heading-style3 .block-title .title-angle-shap:after,
.heading-style3 .widget-title .title-angle-shap:after,
.sidebar .widget .block-title .title-angle-shap:after,
.sidebar .widget .widget-title .title-angle-shap:after {
    width: 12px;
    height: 3px;
    position: absolute;
    top: 0;
    content: "";
    background: #fc4a00;
    left: 0;
}

.vertical-post-grid .col-lg-4 {
    width: 100%;
    margin-right: 15px;
}

.vertical-post-grid .ts-overlay-style {
    padding-bottom: 70%;
    margin-bottom: 15px;
    border-radius: 5px;
}

.ts-author-media .avatar {
    border-radius: 100%;
    display: block;
    margin-bottom: 10px;
}

.ts-author-content {
    padding: 30px;
    border: 1px solid #eaeaea;
    position: relative;
    margin: 20px 0;
}

.ts-author-content a {
    font-weight: 500;
    font-style: italic;
    color: #222222;
    line-height: 20px;
    display: inline-block;
}

.ts-author {
    color: #777777;
}

.ts-author-content::before {
    position: absolute;
    content: "\e944";
    left: 20px;
    top: -10px;
    font-size: 20px;
    font-family: "ts-iconfont";
    background: #fff;
    width: 30px;
    height: 30px;
    text-align: center;
}

.ts-author-content::after {
  position: absolute;
  content: "";
  left: -9px;
  top: 50%;
  width: 16px;
  height: 16px;
  background: #fff;
  -webkit-transform: translateY(-100%) rotate(45deg);
      -ms-transform: translateY(-100%) rotate(45deg);
          transform: translateY(-100%) rotate(45deg);
  border: 1px solid #ddd;
  border-width: 0 0px 1px 1px;
}

@media(min-width: 1024px){
	.ts-footer .container {
	    display: flex;
	    justify-content: space-between;
	}

	.ts-footer .container .col-4{
		width: 30%;
	}

	.recent-posts-widget .post-thumb a{
		min-height: 90px;
	}
    
}

@media(max-width: 1024px){
    .grid-item .ts-overlay-style.featured-post .item {
        min-height: 310px;
    }

    .elementor .elementor-hidden-mobile.show-hidden-sec,
    .elementor .elementor-hidden-phone.show-hidden-sec {
        display: block;
    }
}

@media(max-width: 767px){

    .amp-post-carousel {
        max-height: 330px;
    }

    .digiqole-main-slider .post-meta-info li {
        margin-right: 7px;
    }

    .digiqole-main-slider .post-slide-item {
        padding-bottom: 138%;
    }

    .block-item-post .post-thumb-bg .post-thumb a {
        min-height: 180px;
    }

    .vertical-post-grid .col-lg-4 {
        margin-right: 0;
    }

    .elementor-column.elementor-col-50 .amp-post-carousel {
        margin-right: 0;
    }

    .row .col-md-6{
        width: 100%
    }

    .block-item-post .post-content.feature-contents {
        padding: 30px 0;
    }

    .grid-item .ts-overlay-style.featured-post .item {
        min-height: 310px;
    }

}