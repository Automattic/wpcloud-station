<?php
/**
 * Title: Add Site Form
 * Slug: wpcloud-station/form-add-site
 * Categories: wpcloud_forms
 * Keywords: starter
 * Description: Add new site form.
 *
 * @package wpcloud-station
 */

?>
<!-- wp:wpcloud/nav -->
<nav class="wp-block-wpcloud-nav"><!-- wp:wpcloud/nav-list -->
<ul class="wp-block-wpcloud-nav-list"><!-- wp:wpcloud/nav-item {"url":"/sites","text":"Back to Sites","icon":"chevronLeft","iconOnly":true} -->
<li class="wp-block-wpcloud-nav-item"><a href="/sites" class="is-icon-only"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" aria-hidden="true"><path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path></svg></a></li>
<!-- /wp:wpcloud/nav-item -->

<!-- wp:wpcloud/nav-item {"url":"/sites","text":"Back to Sites"} -->
<li class="wp-block-wpcloud-nav-item"><a href="/sites" class=""><span>Back to Sites</span></a></li>
<!-- /wp:wpcloud/nav-item --></ul>
<!-- /wp:wpcloud/nav-list --></nav>
<!-- /wp:wpcloud/nav -->

<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:wpcloud/form-message {"action":"site_create","message":"\u0026lt;a href=\u0022/sites/${response.slug}\u0022\u003e${response.name}\u0026lt;/a\u003e created.","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"vivid-green-cyan","textColor":"white"} -->
<article class="wp-block-wpcloud-form-message has-white-color has-vivid-green-cyan-background-color has-text-color has-background has-link-color display-none wpcloud-form-message wpcloud-form-message--success wpcloud-form-message--dismissable" data-wpcloud-action="site_create" data-message-type="success" role="status" aria-live="polite" data-message-template="&lt;a href=&quot;/sites/${response.slug}&quot;&gt;${response.name}&lt;/a&gt; created."><p></p><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="dismiss" aria-hidden="true"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></article>
<!-- /wp:wpcloud/form-message -->

<!-- wp:wpcloud/form-message {"success":false,"action":"site_create","message":"There was an issue creating the site: ${response.message}","style":{"elements":{"link":{"color":{"text":"var:preset|color|white"}}}},"backgroundColor":"vivid-red","textColor":"white"} -->
<article class="wp-block-wpcloud-form-message has-white-color has-vivid-red-background-color has-text-color has-background has-link-color display-none wpcloud-form-message wpcloud-form-message--error wpcloud-form-message--dismissable" data-wpcloud-action="site_create" data-message-type="error" role="alert" aria-live="assertive" data-message-template="There was an issue creating the site: ${response.message}"><p></p><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="dismiss" aria-hidden="true"><path d="M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"></path></svg></article>
<!-- /wp:wpcloud/form-message --></div>
<!-- /wp:group -->

<!-- wp:wpcloud/site-create -->
<div class="wpcloud-new-site-form wp-block-wpcloud-site-create"><!-- wp:wpcloud/form-rest-api {"endpoint":"/sites","redirect":"/sites/${site.slug}"} -->
<form class="wp-block-wpcloud-form-rest-api wpcloud-block-form" enctype="text/plain" data-rest-api-endpoint="/sites" data-use-station-api="true" data-station-rest-api-version="v1" data-success-redirect="/sites/${site.slug}" method="POST" data-wpcloud-action="site_create" action="#"><!-- wp:wpcloud/form-input {"name":"site_name","label":"","uniqueId":"0407fcfc-6ac3-460c-b3b6-dc2cba6ef9be-site_name"} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--text"><label class="wpcloud-block-form-input__label" for="0407fcfc-6ac3-460c-b3b6-dc2cba6ef9be-site_name"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text"></span><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph -->
<p>Name</p>
<!-- /wp:paragraph -->

<!-- wp:wpcloud/dropdown {"hideChevron":true,"useIcon":true,"icon":"info","metadata":{"name":""}} -->
<details class="wp-block-wpcloud-dropdown dropdown hide-chevron"><summary role="" class=""><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M12 3.2c-4.8 0-8.8 3.9-8.8 8.8 0 4.8 3.9 8.8 8.8 8.8 4.8 0 8.8-3.9 8.8-8.8 0-4.8-4-8.8-8.8-8.8zm0 16c-4 0-7.2-3.3-7.2-7.2C4.8 8 8 4.8 12 4.8s7.2 3.3 7.2 7.2c0 4-3.2 7.2-7.2 7.2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z"></path></svg></summary><ul><!-- wp:wpcloud/list-item -->
<li class="wp-block-wpcloud-list-item"><!-- wp:paragraph {"metadata":{"name":"Item"}} -->
<p>We’ll choose a temporary domain for you to get your started. You’ll be able to change this later.</p>
<!-- /wp:paragraph --></li>
<!-- /wp:wpcloud/list-item --></ul></details>
<!-- /wp:wpcloud/dropdown --></div>
<!-- /wp:group --></span><input type="text" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" placeholder=" " name="site_name" id="0407fcfc-6ac3-460c-b3b6-dc2cba6ef9be-site_name" required aria-required="true"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"php_version","label":"PHP Version","uniqueId":"9154ea58-66a9-4e21-b18c-e0f9af5e2706-php_version","metadata":{"name":"PHP Version"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="9154ea58-66a9-4e21-b18c-e0f9af5e2706-php_version"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">PHP Version</span></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="php_version"><option value="8.1">8.1</option><option value="8.2">8.2</option><option value="8.3">8.3</option><option value="8.4">8.4</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"data_center","label":"Data Center","uniqueId":"ac0a72ca-cdcd-4f7f-b707-3e5b61a65fb6-data_center","metadata":{"name":"Data Center"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="ac0a72ca-cdcd-4f7f-b707-3e5b61a65fb6-data_center"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Data Center</span></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="data_center" value=""><option value="">-</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"select","name":"site_owner_id","label":"Owner","uniqueId":"dc4f02aa-7a46-43d9-a670-66f14e52eb22-site_owner_id","metadata":{"name":"Owner"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--select"><label class="wpcloud-block-form-input__label" for="dc4f02aa-7a46-43d9-a670-66f14e52eb22-site_owner_id"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Owner</span><!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph --></span><div class="wpcloud-form-input--select--wrapper"><select class="wpcloud-block-form-input__input wpcloud-station-form-input__select" aria-label="Select" name="site_owner_id" value=""><option value="">-</option></select></div></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"password","name":"admin_pass","label":"WP Admin Password","uniqueId":"f8e42b4c-11b2-4602-ae9e-768a8c0a7069-admin_pass","metadata":{"name":"Admin Password"}} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--password"><label class="wpcloud-block-form-input__label" for="f8e42b4c-11b2-4602-ae9e-768a8c0a7069-admin_pass"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">WP Admin Password</span></span><span class="wpcloud-block-form-input--password"><input type="password" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" name="admin_pass" id="f8e42b4c-11b2-4602-ae9e-768a8c0a7069-admin_pass" aria-required="false"/><span class="wpcloud-block-form-input--toggle-hidden"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="wpcloud-block-form-input--toggle-hidden--seen" aria-hidden="true"><path d="M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z"></path></svg><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="display:none" class="wpcloud-block-form-input--toggle-hidden--unseen" aria-hidden="true"><path d="M4.67 10.664s-2.09 1.11-2.917 1.582l.494.87 1.608-.914.002.002c.343.502.86 1.17 1.563 1.84.348.33.742.663 1.185.976L5.57 16.744l.858.515 1.02-1.701a9.1 9.1 0 0 0 4.051 1.18V19h1v-2.263a9.1 9.1 0 0 0 4.05-1.18l1.021 1.7.858-.514-1.034-1.723c.442-.313.837-.646 1.184-.977.703-.669 1.22-1.337 1.563-1.839l.002-.003 1.61.914.493-.87c-1.75-.994-2.918-1.58-2.918-1.58l-.003.005a8.29 8.29 0 0 1-.422.689 10.097 10.097 0 0 1-1.36 1.598c-1.218 1.16-3.042 2.293-5.544 2.293-2.503 0-4.327-1.132-5.546-2.293a10.099 10.099 0 0 1-1.359-1.599 8.267 8.267 0 0 1-.422-.689l-.003-.005Z"></path></svg></span></span></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"hidden","name":"hidden","uniqueId":"1b7749a1-cabc-4d92-b62b-8b3b17a282e6-hidden","metadata":{"name":"hidden test"}} -->
<input type="hidden" name="hidden" value="test"/>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/button {"type":"submit","label":"Create Site","addSpinner":true,"spinnerSpinner":"ringResize","spinnerBackground":false,"spinnerSize":"36px"} -->
<div class="wpcloud-block-button__content" style="position:relative"><span class="wpcloud-block-button__label">Create Site</span><svg width="36px" height="36px" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="wpcloud-block-button__spinner visibility-none position-absolute-center" aria-hidden="true"><g><circle cx="12" cy="12" r="9.5" fill="none" stroke-width="3" stroke-linecap="round"><animate attributeName="stroke-dasharray" dur="1.5s" calcMode="spline" values="0 150;42 150;42 150;42 150" keyTimes="0;0.475;0.95;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"></animate><animate attributeName="stroke-dashoffset" dur="1.5s" calcMode="spline" values="0;-16;-59;-59" keyTimes="0;0.475;0.95;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"></animate></circle><animateTransform attributeName="transform" type="rotate" dur="2s" values="0 12 12;360 12 12" repeatCount="indefinite"></animateTransform></g></svg></div>
<!-- /wp:wpcloud/button --></form>
<!-- /wp:wpcloud/form-rest-api --></div>
<!-- /wp:wpcloud/site-create -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
