<?php

use Illuminate\Support\Facades\Route;

/*=======================================================
 ********************* Admin Routes *********************
=======================================================*/

Route::namespace('Admin')->group(function () {

    Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth:admin', 'setLfmPath']], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
        Route::post('summernote/upload', 'SummernoteController@uploadFileManager')->name('lfm.summernote.upload');
    });

    Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin'], function () {
        Route::post('/login', 'LoginController@authenticate')->name('admin.auth');
        Route::get('/mail-form', 'ForgetController@mailForm')->name('admin.forget.form');
        Route::post('/sendmail', 'ForgetController@sendmail')->name('admin.forget.mail');
    });

    Route::group(['prefix' => 'admin', 'middleware' => ['auth:admin', 'checkstatus', 'setLfmPath']], function () {

        // RTL check
        Route::get('/rtlcheck/{langid}', 'LanguageController@rtlcheck')->name('admin.rtlcheck');

        // Summernote image upload
        Route::post('/summernote/upload', 'SummernoteController@upload')->name('admin.summernote.upload');

        // Admin logout Route
        Route::get('/logout', 'LoginController@logout')->name('admin.logout');

        Route::group(['middleware' => 'checkpermission:Dashboard'], function () {
            // Admin Dashboard Routes
            Route::get('/dashboard', 'DashboardController@dashboard')->name('admin.dashboard');
        });

        // Admin Profile Routes
        Route::get('/changePassword', 'ProfileController@changePass')->name('admin.changePass');
        Route::post('/profile/updatePassword', 'ProfileController@updatePassword')->name('admin.updatePassword');
        Route::get('/profile/edit', 'ProfileController@editProfile')->name('admin.editProfile');
        Route::post('/propic/update', 'ProfileController@updatePropic')->name('admin.propic.update');
        Route::post('/profile/update', 'ProfileController@updateProfile')->name('admin.updateProfile');

        Route::group(['middleware' => 'checkpermission:Theme & Home'], function () {
            // Admin Home Version Setting Routes
            Route::get('/home-settings', 'BasicController@homeSettings')->name('admin.homeSettings');
            Route::post('/homeSettings/post', 'BasicController@updateHomeSettings')->name('admin.homeSettings.update');
        });

        Route::group(['middleware' => 'checkpermission:Basic Settings'], function () {

            // Admin File Manager Routes
            Route::get('/file-manager', 'BasicController@fileManager')->name('admin.file-manager');

            // Admin Logo Routes
            Route::get('/logo', 'BasicController@logo')->name('admin.logo');
            Route::post('/logo/post', 'BasicController@updatelogo')->name('admin.logo.update');

            // Admin preloader Routes
            Route::get('/preloader', 'BasicController@preloader')->name('admin.preloader');
            Route::post('/preloader/post', 'BasicController@updatepreloader')->name('admin.preloader.update');

            // Admin Scripts Routes
            Route::get('/feature/settings', 'BasicController@featuresettings')->name('admin.featuresettings');
            Route::post('/feature/settings/update', 'BasicController@updatefeatrue')->name('admin.featuresettings.update');

            // Admin Basic Information Routes
            Route::get('/basicinfo', 'BasicController@basicinfo')->name('admin.basicinfo');
            Route::post('/basicinfo/{langid}/post', 'BasicController@updatebasicinfo')->name('admin.basicinfo.update');

            // Admin Basic Information Routes
            Route::get('/basicinfo', 'BasicController@basicinfo')->name('admin.basicinfo');
            Route::post('/basicinfo/post', 'BasicController@updatebasicinfo')->name('admin.basicinfo.update');

            // Admin Email Settings Routes
            Route::get('/mail-from-admin', 'EmailController@mailFromAdmin')->name('admin.mailFromAdmin');
            Route::post('/mail-from-admin/update', 'EmailController@updateMailFromAdmin')->name('admin.mailfromadmin.update');
            Route::get('/mail-to-admin', 'EmailController@mailToAdmin')->name('admin.mailToAdmin');
            Route::post('/mail-to-admin/update', 'EmailController@updateMailToAdmin')->name('admin.mailtoadmin.update');
            Route::get('/email-templates', 'EmailController@templates')->name('admin.email.templates');
            Route::get('/email-template/{id}/edit', 'EmailController@editTemplate')->name('admin.email.editTemplate');
            Route::post('/emailtemplate/{id}/update', 'EmailController@templateUpdate')->name('admin.email.templateUpdate');

            // Admin Email Settings Routes
            Route::get('/mail-from-admin', 'EmailController@mailFromAdmin')->name('admin.mailFromAdmin');
            Route::post('/mail-from-admin/update', 'EmailController@updateMailFromAdmin')->name('admin.mailfromadmin.update');
            Route::get('/mail-to-admin', 'EmailController@mailToAdmin')->name('admin.mailToAdmin');
            Route::post('/mail-to-admin/update', 'EmailController@updateMailToAdmin')->name('admin.mailtoadmin.update');

            // Admin Support Routes
            Route::get('/support', 'BasicController@support')->name('admin.support');
            Route::post('/support/{langid}/post', 'BasicController@updatesupport')->name('admin.support.update');

            // Admin Page Heading Routes
            Route::get('/heading', 'BasicController@heading')->name('admin.heading');
            Route::post('/heading/{langid}/update', 'BasicController@updateheading')->name('admin.heading.update');

            // Admin Scripts Routes
            Route::get('/script', 'BasicController@script')->name('admin.script');
            Route::post('/script/update', 'BasicController@updatescript')->name('admin.script.update');

            // Admin Social Routes
            Route::get('/social', 'SocialController@index')->name('admin.social.index');
            Route::post('/social/store', 'SocialController@store')->name('admin.social.store');
            Route::get('/social/{id}/edit', 'SocialController@edit')->name('admin.social.edit');
            Route::post('/social/update', 'SocialController@update')->name('admin.social.update');
            Route::post('/social/delete', 'SocialController@delete')->name('admin.social.delete');

            // Admin SEO Information Routes
            Route::get('/seo', 'BasicController@seo')->name('admin.seo');
            Route::post('/seo/{langid}/update', 'BasicController@updateseo')->name('admin.seo.update');

            // Admin Maintanance Mode Routes
            Route::get('/maintainance', 'BasicController@maintainance')->name('admin.maintainance');
            Route::post('/maintainance/update', 'BasicController@updatemaintainance')->name('admin.maintainance.update');

            // Admin Section Customization Routes
            Route::get('/sections', 'BasicController@sections')->name('admin.sections.index');
            Route::post('/sections/update', 'BasicController@updatesections')->name('admin.sections.update');

            // Admin Offer Banner Routes
            Route::get('/announcement', 'BasicController@announcement')->name('admin.announcement');
            Route::post('/announcement/{langid}/update', 'BasicController@updateannouncement')->name('admin.announcement.update');

            // Admin Section Customization Routes
            Route::get('/sections', 'BasicController@sections')->name('admin.sections.index');
            Route::post('/sections/update', 'BasicController@updatesections')->name('admin.sections.update');

            // Admin Section Customization Routes
            Route::get('/sections', 'BasicController@sections')->name('admin.sections.index');
            Route::post('/sections/update', 'BasicController@updatesections')->name('admin.sections.update');

            // Admin Cookie Alert Routes
            Route::get('/cookie-alert', 'BasicController@cookiealert')->name('admin.cookie.alert');
            Route::post('/cookie-alert/{langid}/update', 'BasicController@updatecookie')->name('admin.cookie.update');

            // Admin Payment Gateways
            Route::get('/gateways', 'GatewayController@index')->name('admin.gateway.index');
            Route::post('/stripe/update', 'GatewayController@stripeUpdate')->name('admin.stripe.update');
            Route::post('/paypal/update', 'GatewayController@paypalUpdate')->name('admin.paypal.update');
            Route::post('/paystack/update', 'GatewayController@paystackUpdate')->name('admin.paystack.update');
            Route::post('/paytm/update', 'GatewayController@paytmUpdate')->name('admin.paytm.update');
            Route::post('/flutterwave/update', 'GatewayController@flutterwaveUpdate')->name('admin.flutterwave.update');
            Route::post('/instamojo/update', 'GatewayController@instamojoUpdate')->name('admin.instamojo.update');
            Route::post('/mollie/update', 'GatewayController@mollieUpdate')->name('admin.mollie.update');
            Route::post('/razorpay/update', 'GatewayController@razorpayUpdate')->name('admin.razorpay.update');
            Route::post('/razerms/update', 'GatewayController@razerMSUpdate')->name('admin.razerms.update');
            Route::post('/mercadopago/update', 'GatewayController@mercadopagoUpdate')->name('admin.mercadopago.update');
            Route::post('/payumoney/update', 'GatewayController@payumoneyUpdate')->name('admin.payumoney.update');
            Route::get('/offline/gateways', 'GatewayController@offline')->name('admin.gateway.offline');
            Route::post('/offline/gateway/store', 'GatewayController@store')->name('admin.gateway.offline.store');
            Route::post('/offline/gateway/update', 'GatewayController@update')->name('admin.gateway.offline.update');
            Route::post('/offline/status', 'GatewayController@status')->name('admin.offline.status');
            Route::post('/offline/gateway/delete', 'GatewayController@delete')->name('admin.offline.gateway.delete');

            // Admin Language Routes
            Route::get('/languages', 'LanguageController@index')->name('admin.language.index');
            Route::get('/language/{id}/edit', 'LanguageController@edit')->name('admin.language.edit');
            Route::get('/language/{id}/edit/keyword', 'LanguageController@editKeyword')->name('admin.language.editKeyword');
            Route::post('/language/store', 'LanguageController@store')->name('admin.language.store');
            Route::post('/language/upload', 'LanguageController@upload')->name('admin.language.upload');
            Route::post('/language/{id}/uploadUpdate', 'LanguageController@uploadUpdate')->name('admin.language.uploadUpdate');
            Route::post('/language/{id}/default', 'LanguageController@default')->name('admin.language.default');
            Route::post('/language/{id}/delete', 'LanguageController@delete')->name('admin.language.delete');
            Route::post('/language/update', 'LanguageController@update')->name('admin.language.update');
            Route::post('/language/{id}/update/keyword', 'LanguageController@updateKeyword')->name('admin.language.updateKeyword');

            // Admin Sitemap Routes
            Route::get('/sitemap', 'SitemapController@index')->name('admin.sitemap.index');
            Route::post('/sitemap/store', 'SitemapController@store')->name('admin.sitemap.store');
            Route::get('/sitemap/{id}/update', 'SitemapController@update')->name('admin.sitemap.update');
            Route::post('/sitemap/{id}/delete', 'SitemapController@delete')->name('admin.sitemap.delete');
            Route::post('/sitemap/download', 'SitemapController@download')->name('admin.sitemap.download');

            // Admin Database Backup
            Route::get('/backup', 'BackupController@index')->name('admin.backup.index');
            Route::post('/backup/store', 'BackupController@store')->name('admin.backup.store');
            Route::post('/backup/{id}/delete', 'BackupController@delete')->name('admin.backup.delete');
            Route::post('/backup/download', 'BackupController@download')->name('admin.backup.download');

            // Admin Cache Clear Routes
            Route::get('/cache-clear', 'CacheController@clear')->name('admin.cache.clear');
        });

        Route::group(['middleware' => 'checkpermission:Content Management'], function () {
            // Admin Hero Section (Static Version) Routes
            Route::get('/herosection/static', 'HerosectionController@static')->name('admin.herosection.static');
            Route::post('/herosection/{langid}/update', 'HerosectionController@update')->name('admin.herosection.update');

            // Admin Hero Section (Slider Version) Routes
            Route::get('/herosection/sliders', 'SliderController@index')->name('admin.slider.index');
            Route::post('/herosection/slider/store', 'SliderController@store')->name('admin.slider.store');
            Route::get('/herosection/slider/{id}/edit', 'SliderController@edit')->name('admin.slider.edit');
            Route::post('/herosection/sliderupdate', 'SliderController@update')->name('admin.slider.update');
            Route::post('/herosection/slider/delete', 'SliderController@delete')->name('admin.slider.delete');

            // Admin Hero Section (Video Version) Routes
            Route::get('/herosection/video', 'HerosectionController@video')->name('admin.herosection.video');
            Route::post('/herosection/video/{langid}/update', 'HerosectionController@videoupdate')->name('admin.herosection.video.update');

            // Admin Hero Section (Parallax Version) Routes
            Route::get('/herosection/parallax', 'HerosectionController@parallax')->name('admin.herosection.parallax');
            Route::post('/herosection/parallax/update', 'HerosectionController@parallaxupdate')->name('admin.herosection.parallax.update');

            // Admin Feature Routes
            Route::get('/features', 'FeatureController@index')->name('admin.feature.index');
            Route::post('/feature/store', 'FeatureController@store')->name('admin.feature.store');
            Route::get('/feature/{id}/edit', 'FeatureController@edit')->name('admin.feature.edit');
            Route::post('/feature/update', 'FeatureController@update')->name('admin.feature.update');
            Route::post('/feature/delete', 'FeatureController@delete')->name('admin.feature.delete');

            // Admin Intro Section Routes
            Route::get('/introsection', 'IntrosectionController@index')->name('admin.introsection.index');
            Route::post('/introsection/{langid}/update', 'IntrosectionController@update')->name('admin.introsection.update');

            // Admin Service Section Routes
            Route::get('/servicesection', 'ServicesectionController@index')->name('admin.servicesection.index');
            Route::post('/servicesection/{langid}/update', 'ServicesectionController@update')->name('admin.servicesection.update');

            // Admin Approach Section Routes
            Route::get('/approach', 'ApproachController@index')->name('admin.approach.index');
            Route::post('/approach/store', 'ApproachController@store')->name('admin.approach.point.store');
            Route::get('/approach/{id}/pointedit', 'ApproachController@pointedit')->name('admin.approach.point.edit');
            Route::post('/approach/{langid}/update', 'ApproachController@update')->name('admin.approach.update');
            Route::post('/approach/pointupdate', 'ApproachController@pointupdate')->name('admin.approach.point.update');
            Route::post('/approach/pointdelete', 'ApproachController@pointdelete')->name('admin.approach.pointdelete');

            // Admin Statistic Section Routes
            Route::get('/statistics', 'StatisticsController@index')->name('admin.statistics.index');
            Route::post('/statistics/{langid}/upload', 'StatisticsController@upload')->name('admin.statistics.upload');
            Route::post('/statistics/store', 'StatisticsController@store')->name('admin.statistics.store');
            Route::get('/statistics/{id}/edit', 'StatisticsController@edit')->name('admin.statistics.edit');
            Route::post('/statistics/update', 'StatisticsController@update')->name('admin.statistics.update');
            Route::post('/statistics/delete', 'StatisticsController@delete')->name('admin.statistics.delete');

            // Admin Call to Action Section Routes
            Route::get('/cta', 'CtaController@index')->name('admin.cta.index');
            Route::post('/cta/{langid}/update', 'CtaController@update')->name('admin.cta.update');

            // Admin Portfolio Section Routes
            Route::get('/portfoliosection', 'PortfoliosectionController@index')->name('admin.portfoliosection.index');
            Route::post('/portfoliosection/{langid}/update', 'PortfoliosectionController@update')->name('admin.portfoliosection.update');

            // Admin Testimonial Routes
            Route::get('/testimonials', 'TestimonialController@index')->name('admin.testimonial.index');
            Route::get('/testimonial/create', 'TestimonialController@create')->name('admin.testimonial.create');
            Route::post('/testimonial/store', 'TestimonialController@store')->name('admin.testimonial.store');
            Route::get('/testimonial/{id}/edit', 'TestimonialController@edit')->name('admin.testimonial.edit');
            Route::post('/testimonial/update', 'TestimonialController@update')->name('admin.testimonial.update');
            Route::post('/testimonial/delete', 'TestimonialController@delete')->name('admin.testimonial.delete');
            Route::post('/testimonialtext/{langid}/update', 'TestimonialController@textupdate')->name('admin.testimonialtext.update');

            // Admin Blog Section Routes
            Route::get('/blogsection', 'BlogsectionController@index')->name('admin.blogsection.index');
            Route::post('/blogsection/{langid}/update', 'BlogsectionController@update')->name('admin.blogsection.update');

            // Admin Partner Routes
            Route::get('/partners', 'PartnerController@index')->name('admin.partner.index');
            Route::post('/partner/store', 'PartnerController@store')->name('admin.partner.store');
            Route::get('/partner/{id}/edit', 'PartnerController@edit')->name('admin.partner.edit');
            Route::post('/partner/update', 'PartnerController@update')->name('admin.partner.update');
            Route::post('/partner/delete', 'PartnerController@delete')->name('admin.partner.delete');

            // Admin Member Routes
            Route::get('/members', 'MemberController@index')->name('admin.member.index');
            Route::get('/member/create', 'MemberController@create')->name('admin.member.create');
            Route::post('/member/store', 'MemberController@store')->name('admin.member.store');
            Route::get('/member/{id}/edit', 'MemberController@edit')->name('admin.member.edit');
            Route::post('/member/update', 'MemberController@update')->name('admin.member.update');
            Route::post('/member/delete', 'MemberController@delete')->name('admin.member.delete');
            Route::post('/teamtext/{langid}/update', 'MemberController@textupdate')->name('admin.teamtext.update');
            Route::post('/member/feature', 'MemberController@feature')->name('admin.member.feature');

            // Admin Package Background Routes
            Route::get('/package/background', 'PackageController@background')->name('admin.package.background');
            Route::post('/package/{langid}/background-upload', 'PackageController@uploadBackground')->name('admin.package.background.upload');

            // Admin Footer Logo Text Routes
            Route::get('/footers', 'FooterController@index')->name('admin.footer.index');
            Route::post('/footer/{langid}/update', 'FooterController@update')->name('admin.footer.update');

            // Admin Ulink Routes
            Route::get('/ulinks', 'UlinkController@index')->name('admin.ulink.index');
            Route::get('/ulink/create', 'UlinkController@create')->name('admin.ulink.create');
            Route::post('/ulink/store', 'UlinkController@store')->name('admin.ulink.store');
            Route::get('/ulink/{id}/edit', 'UlinkController@edit')->name('admin.ulink.edit');
            Route::post('/ulink/update', 'UlinkController@update')->name('admin.ulink.update');
            Route::post('/ulink/delete', 'UlinkController@delete')->name('admin.ulink.delete');

            // Service Settings Route
            Route::get('/service/settings', 'ServiceController@settings')->name('admin.service.settings');
            Route::post('/service/updateSettings', 'ServiceController@updateSettings')->name('admin.service.updateSettings');

            // Admin Service Category Routes
            Route::get('/scategorys', 'ScategoryController@index')->name('admin.scategory.index');
            Route::post('/scategory/store', 'ScategoryController@store')->name('admin.scategory.store');
            Route::get('/scategory/{id}/edit', 'ScategoryController@edit')->name('admin.scategory.edit');
            Route::post('/scategory/update', 'ScategoryController@update')->name('admin.scategory.update');
            Route::post('/scategory/delete', 'ScategoryController@delete')->name('admin.scategory.delete');
            Route::post('/scategory/bulk-delete', 'ScategoryController@bulkDelete')->name('admin.scategory.bulk.delete');
            Route::post('/scategory/feature', 'ScategoryController@feature')->name('admin.scategory.feature');

            // Admin Services Routes
            Route::get('/services', 'ServiceController@index')->name('admin.service.index');
            Route::post('/service/store', 'ServiceController@store')->name('admin.service.store');
            Route::get('/service/{id}/edit', 'ServiceController@edit')->name('admin.service.edit');
            Route::post('/service/update', 'ServiceController@update')->name('admin.service.update');
            Route::post('/service/delete', 'ServiceController@delete')->name('admin.service.delete');
            Route::post('/service/bulk-delete', 'ServiceController@bulkDelete')->name('admin.service.bulk.delete');
            Route::get('/service/{langid}/getcats', 'ServiceController@getcats')->name('admin.service.getcats');
            Route::post('/service/feature', 'ServiceController@feature')->name('admin.service.feature');
            Route::post('/service/sidebar', 'ServiceController@sidebar')->name('admin.service.sidebar');

            // Admin Portfolio Routes
            Route::get('/portfolios', 'PortfolioController@index')->name('admin.portfolio.index');
            Route::get('/portfolio/create', 'PortfolioController@create')->name('admin.portfolio.create');
            Route::post('/portfolio/sliderstore', 'PortfolioController@sliderstore')->name('admin.portfolio.sliderstore');
            Route::post('/portfolio/sliderrmv', 'PortfolioController@sliderrmv')->name('admin.portfolio.sliderrmv');
            Route::post('/portfolio/store', 'PortfolioController@store')->name('admin.portfolio.store');
            Route::get('/portfolio/{id}/edit', 'PortfolioController@edit')->name('admin.portfolio.edit');
            Route::get('/portfolio/{id}/images', 'PortfolioController@images')->name('admin.portfolio.images');
            Route::post('/portfolio/sliderupdate', 'PortfolioController@sliderupdate')->name('admin.portfolio.sliderupdate');
            Route::post('/portfolio/update', 'PortfolioController@update')->name('admin.portfolio.update');
            Route::post('/portfolio/delete', 'PortfolioController@delete')->name('admin.portfolio.delete');
            Route::post('/portfolio/bulk-delete', 'PortfolioController@bulkDelete')->name('admin.portfolio.bulk.delete');
            Route::get('portfolio/{id}/getservices', 'PortfolioController@getservices')->name('admin.portfolio.getservices');
            Route::post('/portfolio/feature', 'PortfolioController@feature')->name('admin.portfolio.feature');

            // Admin Blog Category Routes
            Route::get('/bcategorys', 'BcategoryController@index')->name('admin.bcategory.index');
            Route::post('/bcategory/store', 'BcategoryController@store')->name('admin.bcategory.store');
            Route::post('/bcategory/update', 'BcategoryController@update')->name('admin.bcategory.update');
            Route::post('/bcategory/delete', 'BcategoryController@delete')->name('admin.bcategory.delete');
            Route::post('/bcategory/bulk-delete', 'BcategoryController@bulkDelete')->name('admin.bcategory.bulk.delete');

            // Admin Blog Routes
            Route::get('/blogs', 'BlogController@index')->name('admin.blog.index');
            Route::post('/blog/store', 'BlogController@store')->name('admin.blog.store');
            Route::get('/blog/{id}/edit', 'BlogController@edit')->name('admin.blog.edit');
            Route::post('/blog/update', 'BlogController@update')->name('admin.blog.update');
            Route::post('/blog/delete', 'BlogController@delete')->name('admin.blog.delete');
            Route::post('/blog/bulk-delete', 'BlogController@bulkDelete')->name('admin.blog.bulk.delete');
            Route::get('/blog/{langid}/getcats', 'BlogController@getcats')->name('admin.blog.getcats');
            Route::post('/blog/sidebar', 'BlogController@sidebar')->name('admin.blog.sidebar');

            // Admin Blog Archive Routes
            Route::get('/archives', 'ArchiveController@index')->name('admin.archive.index');
            Route::post('/archive/store', 'ArchiveController@store')->name('admin.archive.store');
            Route::post('/archive/update', 'ArchiveController@update')->name('admin.archive.update');
            Route::post('/archive/delete', 'ArchiveController@delete')->name('admin.archive.delete');

            // Admin Gallery Settings Routes
            Route::get('/gallery/settings', 'GalleryCategoryController@settings')->name('admin.gallery.settings');
            Route::post('/gallery/update_settings', 'GalleryCategoryController@updateSettings')->name('admin.gallery.update_settings');

            // Admin Gallery Category Routes
            Route::get('/gallery/categories', 'GalleryCategoryController@index')->name('admin.gallery.categories');
            Route::post('/gallery/store_category', 'GalleryCategoryController@store')->name('admin.gallery.store_category');
            Route::post('/gallery/update_category', 'GalleryCategoryController@update')->name('admin.gallery.update_category');
            Route::post('/gallery/delete_category', 'GalleryCategoryController@delete')->name('admin.gallery.delete_category');
            Route::post('/gallery/bulk_delete_category', 'GalleryCategoryController@bulkDelete')->name('admin.gallery.bulk_delete_category');

            // Admin Gallery Routes
            Route::get('/gallery', 'GalleryController@index')->name('admin.gallery.index');
            Route::get('/gallery/{langId}/get_categories', 'GalleryController@getCategories');
            Route::post('/gallery/store', 'GalleryController@store')->name('admin.gallery.store');
            Route::get('/gallery/{id}/edit', 'GalleryController@edit')->name('admin.gallery.edit');
            Route::post('/gallery/update', 'GalleryController@update')->name('admin.gallery.update');
            Route::post('/gallery/delete', 'GalleryController@delete')->name('admin.gallery.delete');
            Route::post('/gallery/bulk-delete', 'GalleryController@bulkDelete')->name('admin.gallery.bulk.delete');

            // Admin FAQ Settings Routes
            Route::get('/faq/settings', 'FAQCategoryController@settings')->name('admin.faq.settings');
            Route::post('/faq/update_settings', 'FAQCategoryController@updateSettings')->name('admin.faq.update_settings');

            // Admin FAQ Category Routes
            Route::get('/faq/categories', 'FAQCategoryController@index')->name('admin.faq.categories');
            Route::post('/faq/store_category', 'FAQCategoryController@store')->name('admin.faq.store_category');
            Route::post('/faq/update_category', 'FAQCategoryController@update')->name('admin.faq.update_category');
            Route::post('/faq/delete_category', 'FAQCategoryController@delete')->name('admin.faq.delete_category');
            Route::post('/faq/bulk_delete_category', 'FAQCategoryController@bulkDelete')->name('admin.faq.bulk_delete_category');

            // Admin FAQ Routes
            Route::get('/faqs', 'FaqController@index')->name('admin.faq.index');
            Route::get('/faq/create', 'FaqController@create')->name('admin.faq.create');
            Route::get('/faq/{langId}/get_categories', 'FaqController@getCategories');
            Route::post('/faq/store', 'FaqController@store')->name('admin.faq.store');
            Route::get('/faq/{id}/edit', 'FaqController@edit')->name('admin.faq.edit');
            Route::post('/faq/update', 'FaqController@update')->name('admin.faq.update');
            Route::post('/faq/delete', 'FaqController@delete')->name('admin.faq.delete');
            Route::post('/faq/bulk-delete', 'FaqController@bulkDelete')->name('admin.faq.bulk.delete');

            // Admin Job Category Routes
            Route::get('/jcategorys', 'JcategoryController@index')->name('admin.jcategory.index');
            Route::post('/jcategory/store', 'JcategoryController@store')->name('admin.jcategory.store');
            Route::get('/jcategory/{id}/edit', 'JcategoryController@edit')->name('admin.jcategory.edit');
            Route::post('/jcategory/update', 'JcategoryController@update')->name('admin.jcategory.update');
            Route::post('/jcategory/delete', 'JcategoryController@delete')->name('admin.jcategory.delete');
            Route::post('/jcategory/bulk-delete', 'JcategoryController@bulkDelete')->name('admin.jcategory.bulk.delete');

            // Admin Jobs Routes
            Route::get('/jobs', 'JobController@index')->name('admin.job.index');
            Route::get('/job/create', 'JobController@create')->name('admin.job.create');
            Route::post('/job/store', 'JobController@store')->name('admin.job.store');
            Route::get('/job/{id}/edit', 'JobController@edit')->name('admin.job.edit');
            Route::post('/job/update', 'JobController@update')->name('admin.job.update');
            Route::post('/job/delete', 'JobController@delete')->name('admin.job.delete');
            Route::post('/job/bulk-delete', 'JobController@bulkDelete')->name('admin.job.bulk.delete');
            Route::get('/job/{langid}/getcats', 'JobController@getcats')->name('admin.job.getcats');

            // Admin Contact Routes
            Route::get('/contact', 'ContactController@index')->name('admin.contact.index');
            Route::post('/contact/{langid}/post', 'ContactController@update')->name('admin.contact.update');
        });

        Route::group(['middleware' => 'checkpermission:Menu Builder'], function () {
            // Mega Menus Management Routes
            Route::get('/megamenus', 'MenuBuilderController@megamenus')->name('admin.megamenus');
            Route::get('/megamenus/edit', 'MenuBuilderController@megaMenuEdit')->name('admin.megamenu.edit');
            Route::post('/megamenus/update', 'MenuBuilderController@megaMenuUpdate')->name('admin.megamenu.update');

            // Menus Builder Management Routes
            Route::get('/menu-builder', 'MenuBuilderController@index')->name('admin.menu_builder.index');
            Route::post('/menu-builder/update', 'MenuBuilderController@update')->name('admin.menu_builder.update');

            // Permalinks Routes
            Route::get('/permalinks', 'MenuBuilderController@permalinks')->name('admin.permalinks.index');
            Route::post('/permalinks/update', 'MenuBuilderController@permalinksUpdate')->name('admin.permalinks.update');
        });

        Route::group(['middleware' => 'checkpermission:Announcement Popup'], function () {
            Route::get('popups', 'PopupController@index')->name('admin.popup.index');
            Route::get('popup/types', 'PopupController@types')->name('admin.popup.types');
            Route::get('popup/{id}/edit', 'PopupController@edit')->name('admin.popup.edit');
            Route::get('popup/create', 'PopupController@create')->name('admin.popup.create');
            Route::post('popup/store', 'PopupController@store')->name('admin.popup.store');
            Route::post('popup/delete', 'PopupController@delete')->name('admin.popup.delete');
            Route::post('popup/bulk-delete', 'PopupController@bulkDelete')->name('admin.popup.bulk.delete');
            Route::post('popup/status', 'PopupController@status')->name('admin.popup.status');
            Route::post('popup/update', 'PopupController@update')->name('admin.popup.update');
        });

        Route::group(['middleware' => 'checkpermission:Pages'], function () {
            // Menu Manager Routes
            Route::get('/pages', 'PageController@index')->name('admin.page.index');
            Route::get('/page/settings', 'PageController@settings')->name('admin.page.settings');
            Route::post('/page/update-settings', 'PageController@updateSettings')->name('admin.page.updateSettings');
            Route::get('/page/create', 'PageController@create')->name('admin.page.create');
            Route::post('/page/store', 'PageController@store')->name('admin.page.store');
            Route::get('/page/{menuID}/edit', 'PageController@edit')->name('admin.page.edit');
            Route::post('/page/update', 'PageController@update')->name('admin.page.update');
            Route::post('/page/delete', 'PageController@delete')->name('admin.page.delete');
            Route::post('/page/bulk-delete', 'PageController@bulkDelete')->name('admin.page.bulk.delete');
            Route::post('/upload/pagebuilder', 'PageController@uploadPbImage')->name('admin.pb.upload');
            Route::post('/remove/img/pagebuilder', 'PageController@removePbImage')->name('admin.pb.remove');
            Route::post('/upload/tui/pagebuilder', 'PageController@uploadPbTui')->name('admin.pb.tui.upload');
        });

        // Page Builder Routes
        Route::get('/pagebuilder/content', 'PageBuilderController@content')->name('admin.pagebuilder.content');
        Route::post('/pagebuilder/save', 'PageBuilderController@save')->name('admin.pagebuilder.save');

        Route::group(['middleware' => 'checkpermission:Shop Management'], function () {
            Route::get('/banner', 'SlideBannerController@index')->name('admin.banner.index');
            Route::post('/banner/store', 'SlideBannerController@store')->name('admin.banner.store');
            Route::get('/banner/{id}/edit', 'SlideBannerController@edit')->name('admin.banner.edit');
            Route::post('/banner/update', 'SlideBannerController@update')->name('admin.banner.update');
            Route::post('/banner/feature', 'SlideBannerController@feature')->name('admin.banner.feature');
            Route::post('/banner/delete', 'SlideBannerController@delete')->name('admin.banner.delete');

            Route::get('/category', 'ProductCategory@index')->name('admin.category.index');
            Route::post('/category/store', 'ProductCategory@store')->name('admin.category.store');
            Route::get('/category/{id}/edit', 'ProductCategory@edit')->name('admin.category.edit');
            Route::post('/category/update', 'ProductCategory@update')->name('admin.category.update');
            Route::post('/category/feature', 'ProductCategory@feature')->name('admin.category.feature');
            Route::post('/category/home', 'ProductCategory@home')->name('admin.category.home');
            Route::post('/category/delete', 'ProductCategory@delete')->name('admin.category.delete');
            Route::post('/category/bulk-delete', 'ProductCategory@bulkDelete')->name('admin.pcategory.bulk.delete');

            Route::get('/shipping', 'ShopSettingController@index')->name('admin.shipping.index');
            Route::post('/shipping/store', 'ShopSettingController@store')->name('admin.shipping.store');
            Route::get('/shipping/{id}/edit', 'ShopSettingController@edit')->name('admin.shipping.edit');
            Route::post('/shipping/update', 'ShopSettingController@update')->name('admin.shipping.update');
            Route::post('/shipping/delete', 'ShopSettingController@delete')->name('admin.shipping.delete');

            Route::get('/product', 'ProductController@index')->name('admin.product.index');
            Route::get('/product/type', 'ProductController@type')->name('admin.product.type');
            Route::get('/product/create', 'ProductController@create')->name('admin.product.create');
            Route::post('/product/store', 'ProductController@store')->name('admin.product.store');
            Route::get('/product/{id}/edit', 'ProductController@edit')->name('admin.product.edit');
            Route::post('/product/update', 'ProductController@update')->name('admin.product.update');
            Route::post('/product/feature', 'ProductController@feature')->name('admin.product.feature');
            Route::post('/product/delete', 'ProductController@delete')->name('admin.product.delete');
            Route::get('/product/populer/tags/', 'ProductController@populerTag')->name('admin.product.tags');
            Route::post('/product/populer/tags/update', 'ProductController@populerTagupdate')->name('admin.popular-tag.update');
            Route::post('/product/paymentStatus', 'ProductController@paymentStatus')->name('admin.product.paymentStatus');

            Route::get('product/{id}/getcategory', 'ProductController@getCategory')->name('admin.product.getcategory');
            Route::post('/product/delete', 'ProductController@delete')->name('admin.product.delete');
            Route::post('/product/bulk-delete', 'ProductController@bulkDelete')->name('admin.product.bulk.delete');
            Route::post('/product/sliderupdate', 'ProductController@sliderupdate')->name('admin.product.sliderupdate');
            Route::post('/product/{id}/uploadUpdate', 'ProductController@uploadUpdate')->name('admin.product.uploadUpdate');
            Route::post('/product/update', 'ProductController@update')->name('admin.product.update');
            Route::get('/product/{id}/images', 'ProductController@images')->name('admin.product.images');

            Route::get('/product/settings', 'ProductController@settings')->name('admin.product.settings');
            Route::post('/product/settings', 'ProductController@updateSettings')->name('admin.product.settings');

            // Admin Coupon Routes
            Route::get('/{coupon_type}/coupon', 'CouponController@index')->name('admin.coupon.index');
            Route::post('/coupon/store', 'CouponController@store')->name('admin.coupon.store');
            Route::get('/{coupon_type}/coupon/{id}/edit', 'CouponController@edit')->name('admin.coupon.edit');
            Route::post('/coupon/update', 'CouponController@update')->name('admin.coupon.update');
            Route::post('/coupon/delete', 'CouponController@delete')->name('admin.coupon.delete');
            // Admin Coupon Routes End

            // Product Order
            Route::get('/product/all/orders', 'ProductOrderController@all')->name('admin.all.product.orders');
            Route::get('/product/pending/orders', 'ProductOrderController@pending')->name('admin.pending.product.orders');
            Route::get('/product/processing/orders', 'ProductOrderController@processing')->name('admin.processing.product.orders');
            Route::get('/product/completed/orders', 'ProductOrderController@completed')->name('admin.completed.product.orders');
            Route::get('/product/rejected/orders', 'ProductOrderController@rejected')->name('admin.rejected.product.orders');
            Route::post('/product/orders/status', 'ProductOrderController@status')->name('admin.product.orders.status');
            Route::get('/product/orders/detais/{id}', 'ProductOrderController@details')->name('admin.product.details');
            Route::get('/product/orders/invoice/{id}', 'ProductOrderController@invoice')->name('admin.product.invoice');
            Route::post('/product/order/delete', 'ProductOrderController@orderDelete')->name('admin.product.order.delete');
            Route::post('/product/order/bulk-delete', 'ProductOrderController@bulkOrderDelete')->name('admin.product.order.bulk.delete');
            Route::get('/product/orders/report', 'ProductOrderController@report')->name('admin.orders.report');
            Route::get('/product/export/report', 'ProductOrderController@exportReport')->name('admin.orders.export');
            // Product Order end
        });

        //Event Manage Routes
        Route::group(['middleware' => 'checkpermission:Events Management'], function () {
            Route::get('/event/categories', 'EventCategoryController@index')->name('admin.event.category.index');
            Route::post('/event/category/store', 'EventCategoryController@store')->name('admin.event.category.store');
            Route::post('/event/category/update', 'EventCategoryController@update')->name('admin.event.category.update');
            Route::post('/event/category/delete', 'EventCategoryController@delete')->name('admin.event.category.delete');
            Route::post('/event/categories/bulk-delete', 'EventCategoryController@bulkDelete')->name('admin.event.category.bulk.delete');

            // Admin Event Routes
            Route::get('/event/settings', 'EventController@settings')->name('admin.event.settings');
            Route::post('/event/settings', 'EventController@updateSettings')->name('admin.event.settings');
            Route::get('/events', 'EventController@index')->name('admin.event.index');
            Route::post('/events/{event}/update_status', 'EventController@update_status')->name('admin.event.update_status');
            Route::post('/event/upload', 'EventController@upload')->name('admin.event.upload');
            Route::post('/event/slider/remove', 'EventController@sliderRemove')->name('admin.event.slider-remove');
            Route::post('/event/store', 'EventController@store')->name('admin.event.store');
            Route::get('/event/{id}/edit', 'EventController@edit')->name('admin.event.edit');
            Route::get('/event/{id}/images', 'EventController@images')->name('admin.event.images');
            Route::post('/event/update', 'EventController@update')->name('admin.event.update');
            Route::post('/event/{id}/uploadUpdate', 'EventController@uploadUpdate')->name('admin.event.uploadUpdate');
            Route::post('/event/delete', 'EventController@delete')->name('admin.event.delete');
            Route::post('/event/bulk-delete', 'EventController@bulkDelete')->name('admin.event.bulk.delete');
            Route::get('/event/{lang_id}/get-categories', 'EventController@getCategories')->name('admin.event.get-categories');
            Route::get('/events/payment-log', 'EventController@paymentLog')->name('admin.event.payment.log');
            Route::post('/events/generate-ticket/{id}/{trxId}', 'EventController@generateTicket')->name('admin.event.ticket-generate');
            Route::post('/events/regenerate-ticket/{id}/{trxId}', 'EventController@regenerateTicket')->name('admin.event.ticket-regenerate');
            Route::post('/events/payment-log/delete', 'EventController@paymentLogDelete')->name('admin.event.payment.delete');
            Route::post('/events/payment/bulk-delete', 'EventController@paymentLogBulkDelete')->name('admin.event.payment.bulk.delete');
            Route::post('/events/payment-log-update', 'EventController@paymentLogUpdate')->name('admin.event.payment.log.update');
            Route::get('/events/report', 'EventController@report')->name('admin.event.report');
            Route::get('/events/certificate', 'EventController@certificate')->name('admin.event.certificate');
            Route::get('/events/export', 'EventController@exportReport')->name('admin.event.export');
            Route::post('/events/export/attendance', 'EventController@eventReportAttendance')->name('admin.event.attendance');
            Route::get('/events/export/attendance/send-all', 'EventController@eventReportAttendanceWithoutRequest')->name('admin.event.attendance.send-all');
            Route::post('/events/certificate/download/{event_detail_id}', 'EventController@eventReportCertificateDownload')->name('admin.event.certificate.download');
            Route::post('/events/certificate/regen/{event_detail_id}', 'EventController@eventReportCertificateRegenerate')->name('admin.event.certificate.regenerate');
        });

        //Donation Manage Routes
        Route::group(['middleware' => 'checkpermission:Donation Management'], function () {
            Route::get('/donations', 'DonationController@index')->name('admin.donation.index');
            Route::get('/donation/settings', 'DonationController@settings')->name('admin.donation.settings');
            Route::post('/donation/settings', 'DonationController@updateSettings')->name('admin.donation.settings');
            Route::post('/donation/store', 'DonationController@store')->name('admin.donation.store');
            Route::get('/donation/{id}/edit', 'DonationController@edit')->name('admin.donation.edit');
            Route::post('/donation/update', 'DonationController@update')->name('admin.donation.update');
            Route::post('/donation/{id}/uploadUpdate', 'DonationController@uploadUpdate')->name('admin.donation.uploadUpdate');
            Route::post('/donation/delete', 'DonationController@delete')->name('admin.donation.delete');
            Route::post('/donation/bulk-delete', 'DonationController@bulkDelete')->name('admin.donation.bulk.delete');
            Route::get('/donations/payment-log', 'DonationController@paymentLog')->name('admin.donation.payment.log');
            Route::post('/donations/payment/delete', 'DonationController@paymentDelete')->name('admin.donation.payment.delete');
            Route::post('/donations/bulk/delete', 'DonationController@bulkPaymentDelete')->name('admin.donation.payment.bulk.delete');
            Route::post('/donations/payment-log-update', 'DonationController@paymentLogUpdate')->name('admin.donation.payment.log.update');
            Route::get('/donation/report', 'DonationController@report')->name('admin.donation.report');
            Route::get('/donation/export', 'DonationController@exportReport')->name('admin.donation.export');
        });

        // Admin Event Calendar Routes
        Route::group(['middleware' => 'checkpermission:Event Calendar'], function () {
            Route::get('/calendars', 'CalendarController@index')->name('admin.calendar.index');
            Route::post('/calendar/store', 'CalendarController@store')->name('admin.calendar.store');
            Route::post('/calendar/update', 'CalendarController@update')->name('admin.calendar.update');
            Route::post('/calendar/delete', 'CalendarController@delete')->name('admin.calendar.delete');
            Route::post('/calendar/bulk-delete', 'CalendarController@bulkDelete')->name('admin.calendar.bulk.delete');
        });

        Route::group(['middleware' => 'checkpermission:Knowledgebase'], function () {
            // Admin Article Category Routes
            Route::get('/article_categories', 'ArticleCategoryController@index')->name('admin.article_category.index');
            Route::post('/article_category/store', 'ArticleCategoryController@store')->name('admin.article_category.store');
            Route::post('/article_category/update', 'ArticleCategoryController@update')->name('admin.article_category.update');
            Route::post('/article_category/delete', 'ArticleCategoryController@delete')->name('admin.article_category.delete');
            Route::post('/article_category/bulk_delete', 'ArticleCategoryController@bulkDelete')->name('admin.article_category.bulk_delete');

            // Admin Article Routes
            Route::get('/articles', 'ArticleController@index')->name('admin.article.index');
            Route::get('/article/{langId}/get_categories', 'ArticleController@getCategories');
            Route::post('/article/store', 'ArticleController@store')->name('admin.article.store');
            Route::get('/article/{id}/edit', 'ArticleController@edit')->name('admin.article.edit');
            Route::post('/article/update', 'ArticleController@update')->name('admin.article.update');
            Route::post('/article/delete', 'ArticleController@delete')->name('admin.article.delete');
            Route::post('/article/bulk_delete', 'ArticleController@bulkDelete')->name('admin.article.bulk_delete');
        });

        Route::group(['middleware' => 'checkpermission:Course Management'], function () {
            // Admin Course Category Routes
            Route::get('/course_categories', 'CourseCategoryController@index')->name('admin.course_category.index');
            Route::post('/course_category/store', 'CourseCategoryController@store')->name('admin.course_category.store');
            Route::post('/course_category/update', 'CourseCategoryController@update')->name('admin.course_category.update');
            Route::post('/course_category/delete', 'CourseCategoryController@delete')->name('admin.course_category.delete');
            Route::post('/course_category/bulk_delete', 'CourseCategoryController@bulkDelete')->name('admin.course_category.bulk_delete');

            // Admin Course Routes
            Route::get('/courses', 'CourseController@index')->name('admin.course.index');
            Route::get('/course/create', 'CourseController@create')->name('admin.course.create');
            Route::get('/course/{langId}/get_categories', 'CourseController@getCategories');
            Route::post('/course/store', 'CourseController@store')->name('admin.course.store');
            Route::get('/course/{id}/edit', 'CourseController@edit')->name('admin.course.edit');
            Route::post('/course/update', 'CourseController@update')->name('admin.course.update');
            Route::post('/course/delete', 'CourseController@delete')->name('admin.course.delete');
            Route::post('/course/bulk_delete', 'CourseController@bulkDelete')->name('admin.course.bulk_delete');
            Route::post('/course/featured', 'CourseController@featured')->name('admin.course.featured');
            Route::get('/course/purchase-log', 'CourseController@purchaseLog')->name('admin.course.purchaseLog');
            Route::post('/course/purchase/payment-status', 'CourseController@purchasePaymentStatus')->name('admin.course.purchasePaymentStatus');
            Route::post('/course/purchase/delete', 'CourseController@purchaseDelete')->name('admin.course.purchase.delete');
            Route::post('/course/purchase/delete', 'CourseController@purchaseDelete')->name('admin.course.purchaseDelete');
            Route::post('/course/purchase/bulk_delete', 'CourseController@purchaseBulkOrderDelete')->name('admin.course.purchaseBulkOrderDelete');

            // Admin Course Modules Routes
            Route::get('/course/{id?}/modules', 'ModuleController@index')->name('admin.course.module.index');
            Route::post('/course/module/store', 'ModuleController@store')->name('admin.course.module.store');
            Route::post('/course/module/update', 'ModuleController@update')->name('admin.course.module.update');
            Route::post('/course/module/delete', 'ModuleController@delete')->name('admin.course.module.delete');
            Route::post('/course/module/bulk_delete', 'ModuleController@bulkDelete')->name('admin.course.module.bulk_delete');

            // Admin Module Lessons Routes
            Route::get('/module/{id}/lessons', 'LessonController@index')->name('admin.module.lesson.index');
            Route::post('/module/lesson/store', 'LessonController@store')->name('admin.module.lesson.store');
            Route::post('module/lesson/update', 'LessonController@update')->name('admin.module.lesson.update');
            Route::post('/module/lesson/delete', 'LessonController@delete')->name('admin.module.lesson.delete');
            Route::post('/module/lesson/bulk_delete', 'LessonController@bulkDelete')->name('admin.module.lesson.bulk_delete');

            Route::get('/course/settings', 'CourseController@settings')->name('admin.course.settings');
            Route::post('/course/settings', 'CourseController@updateSettings')->name('admin.course.settings');

            // Admin Course Enroll Report Routes
            Route::get('/course/enrolls/report', 'CourseController@report')->name('admin.enrolls.report');
            Route::get('/course/export/report', 'CourseController@exportReport')->name('admin.enrolls.export');
        });

        Route::group(['middleware' => 'checkpermission:Users Management'], function () {
            // Register User start
            Route::get('register/user/add', 'RegisterUserController@addUser')->name('admin.register.user.add');
            Route::post('register/user/save', 'RegisterUserController@saveUser')->name('admin.register.user.save');
            Route::get('register/users', 'RegisterUserController@index')->name('admin.register.user');
            // add reza 17/2/2024
            Route::get('register/user-member/add', 'RegisterUserController@addUserMember')->name('admin.register.user-member.add');
            Route::post('register/user-member/save', 'RegisterUserController@saveUserMember')->name('admin.register.user-member.save');
            Route::get('register/users-member', 'RegisterUserController@indexMember')->name('admin.register.user-member');
            Route::get('register/users-member/details/{id}', 'RegisterUserController@viewMember')->name('register.user-member.view');
            Route::get('register/users-member/edit/{id}', 'RegisterUserController@editMember')->name('register.user-member.edit');
            Route::post('register/user-member/update/{id}', 'RegisterUserController@update')->name('register.user-member.update');

            Route::post('register/users/ban', 'RegisterUserController@userban')->name('register.user.ban');
            Route::post('register/users/email', 'RegisterUserController@emailStatus')->name('register.user.email');
            Route::get('register/users/details/{id}', 'RegisterUserController@view')->name('register.user.view');
            Route::get('register/users/edit/{id}', 'RegisterUserController@edit')->name('register.user.edit');
            Route::post('register/user/update/membership-id/{id}', 'RegisterUserController@updateMemberId')->name('register.user.membership-id.update');
            Route::post('register/user/update/{id}', 'RegisterUserController@update')->name('register.user.update');
            Route::post('register/user/delete', 'RegisterUserController@delete')->name('register.user.delete');
            Route::post('register/user/bulk-delete', 'RegisterUserController@bulkDelete')->name('register.user.bulk.delete');
            Route::get('register/users/{id}/changePassword', 'RegisterUserController@changePass')->name('register.user.changePass');
            Route::post('register/user/updatePassword', 'RegisterUserController@updatePassword')->name('register.user.updatePassword');
            //Register User end

            Route::post('register/user/subscribe', 'RegisterUserController@activeMembership')->name('admin.user.subscribe');
            Route::post('register/user/updateCpd', 'RegisterUserController@updateCpdPoint')->name('admin.update.cpd-point');
            Route::get('register/user/membership-tracker', 'RegisterUserController@membershipTracker')->name('admin.membership-tracker');
            Route::post('register/user/updateDefReqCpd', 'RegisterUserController@updateDefReqCpdPoint')->name('admin.update.default-required-cpd-point');
            Route::post('register/user/addReqCpd', 'RegisterUserController@addReqCpdPoint')->name('admin.add.req-cpd-point');
            Route::post('register/user/externalCpdList', 'RegisterUserController@externalCpdPoint')->name('admin.cpd.external.point');
            Route::post('register/user/extAttendenceCert', 'RegisterUserController@extAttendenceCert')->name('admin.cpd.external.cert');
            Route::get('register/user/requested-external-cpd', 'RegisterUserController@requestedExtCpdPoint')->name('admin.req-cpd-external');
            Route::post('register/user/resRequestedExtCpd', 'RegisterUserController@responseRequestedExtCpd')->name('admin.res-req-cpd-ext');

            // Admin Push Notification Routes
            Route::get('/pushnotification/settings', 'PushController@settings')->name('admin.pushnotification.settings');
            Route::post('/pushnotification/update/settings', 'PushController@updateSettings')->name('admin.pushnotification.updateSettings');
            Route::get('/pushnotification/send', 'PushController@send')->name('admin.pushnotification.send');
            Route::post('/push', 'PushController@push')->name('admin.pushnotification.push');

            // Admin Subscriber Routes
            Route::get('/subscribers', 'SubscriberController@index')->name('admin.subscriber.index');
            Route::get('/mailsubscriber', 'SubscriberController@mailsubscriber')->name('admin.mailsubscriber');
            Route::post('/subscribers/sendmail', 'SubscriberController@subscsendmail')->name('admin.subscribers.sendmail');
            Route::post('/subscriber/delete', 'SubscriberController@delete')->name('admin.subscriber.delete');
            Route::post('/subscriber/bulk-delete', 'SubscriberController@bulkDelete')->name('admin.subscriber.bulk.delete');
        });

        Route::group(['middleware' => 'checkpermission:Tickets'], function () {
            // Admin Support Ticket Routes
            Route::get('/all/tickets', 'TicketController@all')->name('admin.tickets.all');
            Route::get('/pending/tickets', 'TicketController@pending')->name('admin.tickets.pending');
            Route::get('/open/tickets', 'TicketController@open')->name('admin.tickets.open');
            Route::get('/closed/tickets', 'TicketController@closed')->name('admin.tickets.closed');
            Route::get('/ticket/messages/{id}', 'TicketController@messages')->name('admin.ticket.messages');
            Route::post('/zip-file/upload/', 'TicketController@zip_file_upload')->name('admin.zip_file.upload');
            Route::post('/ticket/reply/{id}', 'TicketController@ticketReply')->name('admin.ticket.reply');
            Route::get('/ticket/close/{id}', 'TicketController@ticketclose')->name('admin.ticket.close');
            Route::post('/ticket/assign/staff', 'TicketController@ticketAssign')->name('ticket.assign.staff');
            Route::get('/ticket/settings', 'TicketController@settings')->name('admin.ticket.settings');
            Route::post('/ticket/settings', 'TicketController@updateSettings')->name('admin.ticket.settings');
        });

        Route::group(['middleware' => 'checkpermission:Package Management'], function () {

            // Admin Package Form Builder Routes
            Route::get('/package/settings', 'PackageController@settings')->name('admin.package.settings');
            Route::post('/package/settings', 'PackageController@updateSettings')->name('admin.package.settings');

            // Admin Package Category Routes
            Route::get('/package/categories', 'PackageCategoryController@index')->name('admin.package.categories');
            Route::post('/package/store_category', 'PackageCategoryController@store')->name('admin.package.store_category');
            Route::post('/package/update_category', 'PackageCategoryController@update')->name('admin.package.update_category');
            Route::post('/package/delete_category', 'PackageCategoryController@delete')->name('admin.package.delete_category');
            Route::post('/package/bulk_delete_category', 'PackageCategoryController@bulkDelete')->name('admin.package.bulk_delete_category');

            Route::get('/package/form', 'PackageController@form')->name('admin.package.form');
            Route::post('/package/form/store', 'PackageController@formstore')->name('admin.package.form.store');
            Route::post('/package/inputDelete', 'PackageController@inputDelete')->name('admin.package.inputDelete');
            Route::get('/package/{id}/inputEdit', 'PackageController@inputEdit')->name('admin.package.inputEdit');
            Route::get('/package/{id}/options', 'PackageController@options')->name('admin.package.options');
            Route::post('/package/inputUpdate', 'PackageController@inputUpdate')->name('admin.package.inputUpdate');
            Route::post('/package/feature', 'PackageController@feature')->name('admin.package.feature');
            Route::post('/package/status', 'PackageController@packageStatus')->name('admin.package.status');

            // Admin Packages Routes
            Route::get('/packages', 'PackageController@index')->name('admin.package.index');
            Route::get('/package/{langId}/get_categories', 'PackageController@getCategories');
            Route::post('/package/store', 'PackageController@store')->name('admin.package.store');
            Route::get('/package/{id}/edit', 'PackageController@edit')->name('admin.package.edit');
            Route::post('/package/update', 'PackageController@update')->name('admin.package.update');
            Route::post('/package/delete', 'PackageController@delete')->name('admin.package.delete');
            Route::post('/package/bulk-delete', 'PackageController@bulkDelete')->name('admin.package.bulk.delete');
            Route::post('/package/payment-status', 'PackageController@paymentStatus')->name('admin.package.paymentStatus');

            // Admin Package Orders Routes
            Route::get('/all/orders', 'PackageController@all')->name('admin.all.orders');
            Route::get('/pending/orders', 'PackageController@pending')->name('admin.pending.orders');
            Route::get('/processing/orders', 'PackageController@processing')->name('admin.processing.orders');
            Route::get('/completed/orders', 'PackageController@completed')->name('admin.completed.orders');
            Route::get('/rejected/orders', 'PackageController@rejected')->name('admin.rejected.orders');
            Route::post('/orders/status', 'PackageController@status')->name('admin.orders.status');
            Route::post('/orders/mail', 'PackageController@mail')->name('admin.orders.mail');
            Route::post('/package/order/delete', 'PackageController@orderDelete')->name('admin.package.order.delete');
            Route::post('/order/bulk-delete', 'PackageController@bulkOrderDelete')->name('admin.order.bulk.delete');
            Route::get('/package/order/report', 'PackageController@report')->name('admin.package.report');
            Route::get('/package/order/export', 'PackageController@exportReport')->name('admin.package.export');

            // Admin Subscription Routes
            Route::get('/subscriptions', 'SubscriptionController@subscriptions')->name('admin.subscriptions');
            Route::get('/subscriptions/active', 'SubscriptionController@activeSubscriptions')->name('admin.active-subs');
            Route::post('/subscriptions/change/date', 'SubscriptionController@subscriptionChangeDate')->name('admin.subscriptions.change.date');
            Route::post('/subscriptions/change/package/{id}', 'SubscriptionController@changePackage')->name('admin.subs.change_package');
            Route::get('/subscription/requests', 'SubscriptionController@requests')->name('admin.requests.subscriptions');
            Route::post('/subscription/mail', 'SubscriptionController@mail')->name('admin.subscription.mail');
            Route::post('/package/subscription/delete', 'SubscriptionController@subDelete')->name('admin.package.subDelete');
            Route::post('/package/subscription/status', 'SubscriptionController@status')->name('admin.subscription.status');
            Route::post('/sub/bulk-delete', 'SubscriptionController@bulkSubDelete')->name('admin.sub.bulk.delete');
        });

        Route::group(['middleware' => 'checkpermission:Quote Management'], function () {

            // Admin Quote Form Builder Routes
            Route::get('/quote/visibility', 'QuoteController@visibility')->name('admin.quote.visibility');
            Route::post('/quote/visibility/update', 'QuoteController@updateVisibility')->name('admin.quote.visibility.update');
            Route::get('/quote/form', 'QuoteController@form')->name('admin.quote.form');
            Route::post('/quote/form/store', 'QuoteController@formstore')->name('admin.quote.form.store');
            Route::post('/quote/inputDelete', 'QuoteController@inputDelete')->name('admin.quote.inputDelete');
            Route::get('/quote/{id}/inputEdit', 'QuoteController@inputEdit')->name('admin.quote.inputEdit');
            Route::get('/quote/{id}/options', 'QuoteController@options')->name('admin.quote.options');
            Route::post('/quote/inputUpdate', 'QuoteController@inputUpdate')->name('admin.quote.inputUpdate');
            Route::post('/quote/delete', 'QuoteController@delete')->name('admin.quote.delete');
            Route::post('/quote/bulk-delete', 'QuoteController@bulkDelete')->name('admin.quote.bulk.delete');

            // Admin Quote Routes
            Route::get('/all/quotes', 'QuoteController@all')->name('admin.all.quotes');
            Route::get('/pending/quotes', 'QuoteController@pending')->name('admin.pending.quotes');
            Route::get('/processing/quotes', 'QuoteController@processing')->name('admin.processing.quotes');
            Route::get('/completed/quotes', 'QuoteController@completed')->name('admin.completed.quotes');
            Route::get('/rejected/quotes', 'QuoteController@rejected')->name('admin.rejected.quotes');
            Route::post('/quotes/status', 'QuoteController@status')->name('admin.quotes.status');
            Route::post('/quote/mail', 'QuoteController@mail')->name('admin.quotes.mail');
        });

        Route::group(['middleware' => 'checkpermission:Quote Management'], function () {

            // Admin Quote Form Builder Routes
            Route::get('/quote/visibility', 'QuoteController@visibility')->name('admin.quote.visibility');
            Route::post('/quote/visibility/update', 'QuoteController@updateVisibility')->name('admin.quote.visibility.update');
            Route::get('/quote/form', 'QuoteController@form')->name('admin.quote.form');
            Route::post('/quote/form/store', 'QuoteController@formstore')->name('admin.quote.form.store');
            Route::post('/quote/inputDelete', 'QuoteController@inputDelete')->name('admin.quote.inputDelete');
            Route::get('/quote/{id}/inputEdit', 'QuoteController@inputEdit')->name('admin.quote.inputEdit');
            Route::get('/quote/{id}/options', 'QuoteController@options')->name('admin.quote.options');
            Route::post('/quote/inputUpdate', 'QuoteController@inputUpdate')->name('admin.quote.inputUpdate');
            Route::post('/quote/delete', 'QuoteController@delete')->name('admin.quote.delete');
            Route::post('/quote/bulk-delete', 'QuoteController@bulkDelete')->name('admin.quote.bulk.delete');

            // Admin Quote Routes
            Route::get('/all/quotes', 'QuoteController@all')->name('admin.all.quotes');
            Route::get('/pending/quotes', 'QuoteController@pending')->name('admin.pending.quotes');
            Route::get('/processing/quotes', 'QuoteController@processing')->name('admin.processing.quotes');
            Route::get('/completed/quotes', 'QuoteController@completed')->name('admin.completed.quotes');
            Route::get('/rejected/quotes', 'QuoteController@rejected')->name('admin.rejected.quotes');
            Route::post('/quotes/status', 'QuoteController@status')->name('admin.quotes.status');
            Route::post('/quote/mail', 'QuoteController@mail')->name('admin.quotes.mail');
        });

        Route::group(['middleware' => 'checkpermission:Role Management'], function () {
            // Admin Roles Routes
            Route::get('/roles', 'RoleController@index')->name('admin.role.index');
            Route::post('/role/store', 'RoleController@store')->name('admin.role.store');
            Route::post('/role/update', 'RoleController@update')->name('admin.role.update');
            Route::post('/role/delete', 'RoleController@delete')->name('admin.role.delete');
            Route::get('role/{id}/permissions/manage', 'RoleController@managePermissions')->name('admin.role.permissions.manage');
            Route::post('role/permissions/update', 'RoleController@updatePermissions')->name('admin.role.permissions.update');
        });

        Route::group(['middleware' => 'checkpermission:Users Management'], function () {
            // Admin Users Routes
            Route::get('/users', 'UserController@index')->name('admin.user.index');
            Route::post('/user/store', 'UserController@store')->name('admin.user.store');
            Route::get('/user/{id}/edit', 'UserController@edit')->name('admin.user.edit');
            Route::post('/user/update', 'UserController@update')->name('admin.user.update');
            Route::post('/user/delete', 'UserController@delete')->name('admin.user.delete');
        });

        Route::group(['middleware' => 'checkpermission:Client Feedbacks'], function () {
            // Admin View Client Feedbacks Routes
            Route::get('/feedbacks', 'FeedbackController@feedbacks')->name('admin.client_feedbacks');
            Route::post('/delete_feedback', 'FeedbackController@deleteFeedback')->name('admin.delete_feedback');
            Route::post('/feedback/bulk-delete', 'FeedbackController@bulkDelete')->name('admin.feedback.bulk.delete');
        });
    });
});
