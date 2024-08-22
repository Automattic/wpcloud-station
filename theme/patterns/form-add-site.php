<?php
/**
 * Title: Add Site Form
 * Slug: wpcloud-station/form-add-site
 * Categories: wpcloud_forms
 * Keywords: starter
 * Description: Add new site form.
 */
?>

<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"},"margin":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-right:0;padding-left:0"><!-- wp:wpcloud/icon {"icon":"chevronLeft"} -->
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="wp-block-wpcloud-icon wpcloud-block-icon" aria-hidden="true"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg>
<!-- /wp:wpcloud/icon -->

<!-- wp:wpcloud/button {"style":"text","label":"Back to Sites","url":"/sites"} -->
<div class="wpcloud-block-button__content"><span class="wpcloud-block-button__label">Back to Sites</span></div>
<!-- /wp:wpcloud/button --></div>
<!-- /wp:group -->

<!-- wp:wpcloud/site-details -->
<div class="wp-block-wpcloud-site-details wpcloud-block-site-detail-card"><!-- wp:group {"className":"wpcloud-site-detail-card","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group wpcloud-site-detail-card"><!-- wp:heading {"level":3,"className":"wpcloud-site-detail-card__title"} -->
<h3 class="wp-block-heading wpcloud-site-detail-card__title">Add Site</h3>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:wpcloud/site-create -->
<div class="wpcloud-new-site-form wp-block-wpcloud-site-create"><!-- wp:wpcloud/form {"wpcloudAction":"site_create"} -->
<form class="wp-block-wpcloud-form wpcloud-block-form" enctype="text/plain"><!-- wp:wpcloud/form-input {"name":"site_name","label":"","uniqueId":"d282294a-d2c3-49e1-9df0-567d7fbd8b95-site_name","metadata":{"name":"Site Name"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--text"><label class="wpcloud-block-form-input__label" for="d282294a-d2c3-49e1-9df0-567d7fbd8b95-site_name"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text"></span><!-- wp:wpcloud/expanding-section {"clickToToggle":true,"hideHeader":false,"openOnLoad":true} -->
<div class="wp-block-wpcloud-expanding-section wpcloud-block-expanding-section click-to-toggle"><!-- wp:wpcloud/expanding-header {"openOnLoad":true,"metadata":{"name":"Site name label"}} -->
<div class="wp-block-wpcloud-expanding-header wpcloud-block-expanding-section__header-wrapper"><div class="wpcloud-block-expanding-section__header"><!-- wp:group {"metadata":{"name":"label"},"className":"wpcloud-block-expanding-section__header","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"left"}} -->
<div class="wp-block-group wpcloud-block-expanding-section__header"><!-- wp:paragraph -->
<p>Name</p>
<!-- /wp:paragraph -->

<!-- wp:wpcloud/button {"type":"action","style":"text","label":"Open","addIcon":true,"iconOnly":true,"action":"wpcloud_expanding_section_toggle","className":"wpcloud-block-expanding-section__toggle\u002d\u002dopen"} -->
<div class="wpcloud-block-button__content"><!-- wp:wpcloud/icon {"icon":"info"} -->
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="wp-block-wpcloud-icon wpcloud-block-icon" aria-hidden="true"><path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path></svg>
<!-- /wp:wpcloud/icon --></div>
<!-- /wp:wpcloud/button --></div>
<!-- /wp:group --></div></div>
<!-- /wp:wpcloud/expanding-header -->

<!-- wp:wpcloud/expanding-content {"openOnLoad":true,"metadata":{"name":"Site name info"}} -->
<div class="wp-block-wpcloud-expanding-content wpcloud-block-expanding-section__content-wrapper is-open"><div class="wpcloud-block-expanding-section__content"><div class="wpcloud-block-expanding-section__content-inner"><!-- wp:group {"metadata":{"name":"content"},"className":"wpcloud-block-expanding-section__content","layout":{"type":"constrained"}} -->
<div class="wp-block-group wpcloud-block-expanding-section__content"><!-- wp:paragraph -->
<p>We’ll choose a temporary domain for you to get your started. You’ll be able to change this later.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div></div></div>
<!-- /wp:wpcloud/expanding-content --></div>
<!-- /wp:wpcloud/expanding-section --></span><input type="text" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" placeholder="Enter site name" name="site_name" id="d282294a-d2c3-49e1-9df0-567d7fbd8b95-site_name" required aria-required="true"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"php_version","label":"PHP Version","options":[{"value":"8.3","label":"8.3"},{"value":"8.2","label":"8.2"},{"value":"8.1","label":"8.1"}],"uniqueId":"97f6ce3e-c95e-4fb8-a239-3b6c57d494a8-php_version","metadata":{"name":"PHP Version"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="97f6ce3e-c95e-4fb8-a239-3b6c57d494a8-php_version"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">PHP Version</span></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="php_version"><option value="8.3">8.3</option><option value="8.2">8.2</option><option value="8.1">8.1</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"data_center","label":"Data Center","options":[{"value":"No Preference","label":"No Preference"},{"value":"Los Angeles, CA","label":"Los Angeles, CA"},{"value":"Washington, D.C., USA","label":"Washington, D.C., USA"},{"value":"Dallas, TX, USA","label":"Dallas, TX, USA"}],"uniqueId":"7c61c369-6c50-49df-b1dd-3fbdad863cba-data_center","metadata":{"name":"Data Center"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="7c61c369-6c50-49df-b1dd-3fbdad863cba-data_center"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Data Center</span></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="data_center"><option value="No Preference">No Preference</option><option value="Los Angeles, CA">Los Angeles, CA</option><option value="Washington, D.C., USA">Washington, D.C., USA</option><option value="Dallas, TX, USA">Dallas, TX, USA</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"site_owner_id","label":"Owner","adminOnly":true,"options":[{"value":"1","label":"Site Owner"}],"uniqueId":"725974f7-356a-4360-9fec-865d1dd8a723-site_owner_id","metadata":{"name":"Owner"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="725974f7-356a-4360-9fec-865d1dd8a723-site_owner_id"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Owner</span></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="site_owner_id"><option value="1">Site Owner</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"password","name":"admin_pass","label":"WP Admin Password","uniqueId":"8a754e48-bac0-4db2-ab17-a9e95879e837-admin_pass","metadata":{"name":"WP Admin Password"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--password"><label class="wpcloud-block-form-input__label" for="8a754e48-bac0-4db2-ab17-a9e95879e837-admin_pass"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">WP Admin Password</span></span><span class="wpcloud-block-form-input--password"><input type="password" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" name="admin_pass" id="8a754e48-bac0-4db2-ab17-a9e95879e837-admin_pass" aria-required="false"/><span class="wpcloud-block-form-input--toggle-hidden"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="wpcloud-block-form-input--toggle-hidden--seen" aria-hidden="true"><path d="M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z"></path></svg><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="display:none" class="wpcloud-block-form-input--toggle-hidden--unseen" aria-hidden="true"><path d="M4.67 10.664s-2.09 1.11-2.917 1.582l.494.87 1.608-.914.002.002c.343.502.86 1.17 1.563 1.84.348.33.742.663 1.185.976L5.57 16.744l.858.515 1.02-1.701a9.1 9.1 0 0 0 4.051 1.18V19h1v-2.263a9.1 9.1 0 0 0 4.05-1.18l1.021 1.7.858-.514-1.034-1.723c.442-.313.837-.646 1.184-.977.703-.669 1.22-1.337 1.563-1.839l.002-.003 1.61.914.493-.87c-1.75-.994-2.918-1.58-2.918-1.58l-.003.005a8.29 8.29 0 0 1-.422.689 10.097 10.097 0 0 1-1.36 1.598c-1.218 1.16-3.042 2.293-5.544 2.293-2.503 0-4.327-1.132-5.546-2.293a10.099 10.099 0 0 1-1.359-1.599 8.267 8.267 0 0 1-.422-.689l-.003-.005Z"></path></svg></span></span></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"checkbox","name":"tos","label":"Any \u003ca href=\u0022https://en.wikipedia.org/wiki/Terms_of_service\u0022\u003eterms and conditions\u003c/a\u003e that we might need to add.","uniqueId":"2d779721-d841-4311-8447-5e325872d59a-tos","metadata":{"name":"TOS"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--checkbox"><label class="wpcloud-block-form-input__label" for="2d779721-d841-4311-8447-5e325872d59a-tos"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Any <a href="https://en.wikipedia.org/wiki/Terms_of_service">terms and conditions</a> that we might need to add.</span></span><input type="checkbox" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" placeholder="Optional placeholder..." name="tos" id="2d779721-d841-4311-8447-5e325872d59a-tos" aria-required="false"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/button {"type":"submit","label":"Create Site","metadata":{"name":"Create Site Button"}} -->
<div class="wpcloud-block-button__content"><span class="wpcloud-block-button__label">Create Site</span></div>
<!-- /wp:wpcloud/button --></form>
<!-- /wp:wpcloud/form --></div>
<!-- /wp:wpcloud/site-create --></div>
<!-- /wp:wpcloud/site-details --></div>
<!-- /wp:group -->
