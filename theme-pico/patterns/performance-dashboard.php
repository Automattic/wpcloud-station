<?php
/**
 * Title: Performance Dashboard
 * Slug: wpcloud-station/performance-dashboard
 * Categories: wpcloud_forms
 * Keywords: starter
 * Description: Performance Dashboard.
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

<!-- wp:group {"tagName":"header","layout":{"inherit":true,"type":"constrained"}} -->
<header class="wp-block-group"><!-- wp:site-title /-->

	<!-- wp:site-tagline /--></header>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"right":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group" style="margin-top:0;margin-bottom:0;padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:wpcloud/metrics -->
	<div class="wp-block-wpcloud-metrics" id="metrics" data-metrics-attributes="{&quot;id&quot;:&quot;metrics&quot;,&quot;type&quot;:&quot;client&quot;}"><!-- wp:group {"metadata":{"name":"Graphs"},"className":"wpcloud-metrics","style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"grid","columnCount":2,"minimumColumnWidth":null}} -->
		<div class="wp-block-group wpcloud-metrics" style="padding-right:0;padding-left:0"><!-- wp:wpcloud/graph {"dimension":"http_status","resolution":"60","type":"stacked-bar","title":"Requests by HTTP Response","allowFrontendFilters":false} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;requests&quot;,&quot;dimension&quot;:&quot;http_status&quot;,&quot;resolution&quot;:&quot;60&quot;,&quot;summarize&quot;:false,&quot;topX&quot;:&quot;20&quot;,&quot;type&quot;:&quot;stacked-bar&quot;,&quot;orientation&quot;:&quot;vertical&quot;,&quot;title&quot;:&quot;Requests by HTTP Response&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"metric":"response_time_average","dimension":"server_protocol","resolution":"60","topX":"10","title":"HTTP Response Time","allowFrontendFilters":false} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;response_time_average&quot;,&quot;dimension&quot;:&quot;server_protocol&quot;,&quot;resolution&quot;:&quot;60&quot;,&quot;summarize&quot;:false,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;line&quot;,&quot;orientation&quot;:&quot;vertical&quot;,&quot;title&quot;:&quot;HTTP Response Time&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"dimension":"http_host","resolution":"10","summarize":true,"type":"bar","orientation":"horizontal","title":"Top 20 Sites by Requests","allowFrontendFilters":false,"style":{"dimensions":{"minHeight":""}}} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;requests&quot;,&quot;dimension&quot;:&quot;http_host&quot;,&quot;resolution&quot;:&quot;10&quot;,&quot;summarize&quot;:true,&quot;topX&quot;:&quot;20&quot;,&quot;type&quot;:&quot;bar&quot;,&quot;orientation&quot;:&quot;horizontal&quot;,&quot;title&quot;:&quot;Top 20 Sites by Requests&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false,&quot;style&quot;:{&quot;dimensions&quot;:{&quot;minHeight&quot;:&quot;&quot;}}}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"dimension":"http_host","summarize":true,"topX":"10","type":"bar","orientation":"horizontal","title":"Top 10 Resource-Limited Sites (429)","predefinedFilters":[["http_status","=",429]],"allowFrontendFilters":false,"metadata":{"name":"Top 10 Resource-Limited Sites (429)"}} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;requests&quot;,&quot;dimension&quot;:&quot;http_host&quot;,&quot;resolution&quot;:10,&quot;summarize&quot;:true,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;bar&quot;,&quot;orientation&quot;:&quot;horizontal&quot;,&quot;title&quot;:&quot;Top 10 Resource-Limited Sites (429)&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[[&quot;http_status&quot;,&quot;=&quot;,429]],&quot;allowFrontendFilters&quot;:false,&quot;metadata&quot;:{&quot;name&quot;:&quot;Top 10 Resource-Limited Sites (429)&quot;}}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"dimension":"http_host","summarize":true,"topX":"10","type":"bar","orientation":"horizontal","title":"Top 10 Sites with 5XX","predefinedFilters":[["http_status","\u003e=",500],["http_status","\u003c",600]],"allowFrontendFilters":false} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;requests&quot;,&quot;dimension&quot;:&quot;http_host&quot;,&quot;resolution&quot;:10,&quot;summarize&quot;:true,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;bar&quot;,&quot;orientation&quot;:&quot;horizontal&quot;,&quot;title&quot;:&quot;Top 10 Sites with 5XX&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[[&quot;http_status&quot;,&quot;&gt;=&quot;,500],[&quot;http_status&quot;,&quot;<&quot;,600]],&quot;allowFrontendFilters&quot;:false}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"metric":"php_cpu_time_persec","dimension":"atomic_site_id","resolution":"60","topX":"10","title":"Top 10 Sites by CPU Cores","allowFrontendFilters":false} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;php_cpu_time_persec&quot;,&quot;dimension&quot;:&quot;atomic_site_id&quot;,&quot;resolution&quot;:&quot;60&quot;,&quot;summarize&quot;:false,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;line&quot;,&quot;orientation&quot;:&quot;vertical&quot;,&quot;title&quot;:&quot;Top 10 Sites by CPU Cores&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"metric":"php_response_time_sum","dimension":"http_host","resolution":"300","topX":"10","title":"Top 10 Sites by Request Time","allowFrontendFilters":false,"style":{"layout":{"columnSpan":1,"rowSpan":1}}} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;php_response_time_sum&quot;,&quot;dimension&quot;:&quot;http_host&quot;,&quot;resolution&quot;:&quot;300&quot;,&quot;summarize&quot;:false,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;line&quot;,&quot;orientation&quot;:&quot;vertical&quot;,&quot;title&quot;:&quot;Top 10 Sites by Request Time&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false,&quot;style&quot;:{&quot;layout&quot;:{&quot;columnSpan&quot;:1,&quot;rowSpan&quot;:1}}}">Graph</div>
			<!-- /wp:wpcloud/graph -->

			<!-- wp:wpcloud/graph {"metric":"response_bytes","dimension":"http_host","resolution":"10","topX":"10","title":"Top 10 Sites By Bandwidth","allowFrontendFilters":false} -->
			<div class="wp-block-wpcloud-graph" data-graph-attributes="{&quot;metric&quot;:&quot;response_bytes&quot;,&quot;dimension&quot;:&quot;http_host&quot;,&quot;resolution&quot;:&quot;10&quot;,&quot;summarize&quot;:false,&quot;topX&quot;:&quot;10&quot;,&quot;type&quot;:&quot;line&quot;,&quot;orientation&quot;:&quot;vertical&quot;,&quot;title&quot;:&quot;Top 10 Sites By Bandwidth&quot;,&quot;showLegend&quot;:true,&quot;minWidth&quot;:&quot;500px&quot;,&quot;predefinedFilters&quot;:[],&quot;allowFrontendFilters&quot;:false}">Graph</div>
			<!-- /wp:wpcloud/graph --></div>
		<!-- /wp:group --></div>
	<!-- /wp:wpcloud/metrics --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p></p>
<!-- /wp:paragraph -->
