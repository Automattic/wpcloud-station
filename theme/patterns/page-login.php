<?php
/**
 * Title: Login Form
 * Slug: wpcloud-station/page-login
 * Categories: wpcloud_forms
 * Keywords: starter
 * Blocks: wpcloud/login
 * Post Types: page
 * Description: Login form.
 */
?>

<!-- wp:wpcloud/site-details -->
<div class="wp-block-wpcloud-site-details wpcloud-block-site-detail-card"><!-- wp:group {"className":"wpcloud-site-detail-card"} -->
<div class="wp-block-group wpcloud-site-detail-card"><!-- wp:site-logo {"width":176} /-->

<!-- wp:spacer {"height":"4px"} -->
<div style="height:4px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"className":"wpcloud-page\u002d\u002dlogin\u002d\u002dheader","style":{"spacing":{"margin":{"top":"0","bottom":"0","left":"0","right":"0"}}}} -->
<h2 class="wp-block-heading wpcloud-page--login--header" style="margin-top:0;margin-right:0;margin-bottom:0;margin-left:0">Welcome to WP Cloud Station</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|foreground-secondary"}}},"spacing":{"padding":{"top":"0px","bottom":"0px","left":"0","right":"0"},"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"textColor":"foreground-secondary"} -->
<p class="has-foreground-secondary-color has-text-color has-link-color" style="margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);padding-top:0px;padding-right:0;padding-bottom:0px;padding-left:0">Login here to manage sites on your hosting</p>
<!-- /wp:paragraph -->

<!-- wp:spacer {"height":"1px"} -->
<div style="height:1px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wpcloud/form {"wpcloudAction":" login","redirect":"/sites"} -->
<form class="wp-block-wpcloud-form wpcloud-block-form" enctype="text/plain"><!-- wp:wpcloud/form-input {"name":"log","label":"Email or Username","uniqueId":"08eca451-aa41-4ac4-9dc5-90999abcc369-log"} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--text"><label class="wpcloud-block-form-input__label" for="08eca451-aa41-4ac4-9dc5-90999abcc369-log"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Email or Username</span></span><input type="text" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" placeholder="youremail@example.com" name="log" id="08eca451-aa41-4ac4-9dc5-90999abcc369-log" aria-required="false"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"password","name":"pwd","label":"Password","uniqueId":"187909ac-15c1-4505-bee8-1aa5ef09d48d-pwd"} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--password"><label class="wpcloud-block-form-input__label" for="187909ac-15c1-4505-bee8-1aa5ef09d48d-pwd"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Password</span></span><span class="wpcloud-block-form-input--password"><input type="password" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" name="pwd" id="187909ac-15c1-4505-bee8-1aa5ef09d48d-pwd" aria-required="false"/><span class="wpcloud-block-form-input--toggle-hidden"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="wpcloud-block-form-input--toggle-hidden--seen" aria-hidden="true"><path d="M3.99961 13C4.67043 13.3354 4.6703 13.3357 4.67017 13.3359L4.67298 13.3305C4.67621 13.3242 4.68184 13.3135 4.68988 13.2985C4.70595 13.2686 4.7316 13.2218 4.76695 13.1608C4.8377 13.0385 4.94692 12.8592 5.09541 12.6419C5.39312 12.2062 5.84436 11.624 6.45435 11.0431C7.67308 9.88241 9.49719 8.75 11.9996 8.75C14.502 8.75 16.3261 9.88241 17.5449 11.0431C18.1549 11.624 18.6061 12.2062 18.9038 12.6419C19.0523 12.8592 19.1615 13.0385 19.2323 13.1608C19.2676 13.2218 19.2933 13.2686 19.3093 13.2985C19.3174 13.3135 19.323 13.3242 19.3262 13.3305L19.3291 13.3359C19.3289 13.3357 19.3288 13.3354 19.9996 13C20.6704 12.6646 20.6703 12.6643 20.6701 12.664L20.6697 12.6632L20.6688 12.6614L20.6662 12.6563L20.6583 12.6408C20.6517 12.6282 20.6427 12.6108 20.631 12.5892C20.6078 12.5459 20.5744 12.4852 20.5306 12.4096C20.4432 12.2584 20.3141 12.0471 20.1423 11.7956C19.7994 11.2938 19.2819 10.626 18.5794 9.9569C17.1731 8.61759 14.9972 7.25 11.9996 7.25C9.00203 7.25 6.82614 8.61759 5.41987 9.9569C4.71736 10.626 4.19984 11.2938 3.85694 11.7956C3.68511 12.0471 3.55605 12.2584 3.4686 12.4096C3.42484 12.4852 3.39142 12.5459 3.36818 12.5892C3.35656 12.6108 3.34748 12.6282 3.34092 12.6408L3.33297 12.6563L3.33041 12.6614L3.32948 12.6632L3.32911 12.664C3.32894 12.6643 3.32879 12.6646 3.99961 13ZM11.9996 16C13.9326 16 15.4996 14.433 15.4996 12.5C15.4996 10.567 13.9326 9 11.9996 9C10.0666 9 8.49961 10.567 8.49961 12.5C8.49961 14.433 10.0666 16 11.9996 16Z"></path></svg><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="16" height="16" style="display:none" class="wpcloud-block-form-input--toggle-hidden--unseen" aria-hidden="true"><path d="M4.67 10.664s-2.09 1.11-2.917 1.582l.494.87 1.608-.914.002.002c.343.502.86 1.17 1.563 1.84.348.33.742.663 1.185.976L5.57 16.744l.858.515 1.02-1.701a9.1 9.1 0 0 0 4.051 1.18V19h1v-2.263a9.1 9.1 0 0 0 4.05-1.18l1.021 1.7.858-.514-1.034-1.723c.442-.313.837-.646 1.184-.977.703-.669 1.22-1.337 1.563-1.839l.002-.003 1.61.914.493-.87c-1.75-.994-2.918-1.58-2.918-1.58l-.003.005a8.29 8.29 0 0 1-.422.689 10.097 10.097 0 0 1-1.36 1.598c-1.218 1.16-3.042 2.293-5.544 2.293-2.503 0-4.327-1.132-5.546-2.293a10.099 10.099 0 0 1-1.359-1.599 8.267 8.267 0 0 1-.422-.689l-.003-.005Z"></path></svg></span></span></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:wpcloud/form-input {"type":"checkbox","name":"remember_me","label":"Remember me","uniqueId":"06ce2ef7-f73e-4f62-839e-887fffbee5f6-remember_me"} -->
<div class="wpcloud-block-form--input wp-block-wpcloud-form-input wpcloud-block-form--input--checkbox"><label class="wpcloud-block-form-input__label" for="06ce2ef7-f73e-4f62-839e-887fffbee5f6-remember_me"><span class="wpcloud-block-form-input__label-content"><span class="wpcloud-block-form-input__label-text">Remember me</span></span><input type="checkbox" class="wpcloud-block-form-input__input" aria-label="Optional placeholder text" placeholder="Optional placeholder..." name="remember_me" id="06ce2ef7-f73e-4f62-839e-887fffbee5f6-remember_me" aria-required="false"/></label></div>
<!-- /wp:wpcloud/form-input -->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:wpcloud/button {"type":"submit","label":"Login"} -->
<div class="wpcloud-block-button__content"><span class="wpcloud-block-button__label">Login</span></div>
<!-- /wp:wpcloud/button --></form>
<!-- /wp:wpcloud/form --></div>
<!-- /wp:group --></div>
<!-- /wp:wpcloud/site-details -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
