(()=>{"use strict";var e,i={878:()=>{const e=window.wp.blocks,i=window.React,t=(window.wp.i18n,window.wp.blockEditor),l=[["core/columns",{verticalAlignment:"center",isStackedOnMobile:!0,width:"100%"},[["core/column",{verticalAlignment:"top"},[]],["core/column",{verticalAlignment:"top"},[["wpcloud/site-detail",{title:"Domain",key:"domain_name",inline:!0}],["wpcloud/site-detail",{title:"PHP Version",key:"php_version",inline:!0}],["wpcloud/site-detail",{title:"Data Center",key:"geo_affinity",inline:!0}],["wpcloud/site-detail",{title:"WP Version",key:"wp_version",inline:!0}],["wpcloud/site-detail",{title:"Admin Email",key:"wp_admin_email",inline:!0}],["wpcloud/site-detail",{title:"Admin User",key:"wp_admin_user",inline:!0}],["wpcloud/site-detail",{title:"IP Addresses",key:"ip_addresses",inline:!0}],["wpcloud/site-detail",{title:"Static 404",key:"static_file_404",inline:!0}]]],["core/column",{verticalAlignment:"top"},[["wpcloud/site-detail",{title:"DB Charset",key:"db_charset",inline:!0}],["wpcloud/site-detail",{title:"DB Collate",key:"db_collate",inline:!0}],["wpcloud/site-detail",{title:"DB Password",key:"db_password",inline:!0}],["wpcloud/site-detail",{title:"DB File Size",key:"db_file_size",inline:!0}],["wpcloud/site-detail",{title:"SMTP Password",key:"smtp_pass",inline:!0}],["wpcloud/site-detail",{title:"Server Pool ID",key:"server_pool_id",inline:!0}],["wpcloud/site-detail",{title:"Atomic Client ID",key:"atomic_client_id",inline:!0}],["wpcloud/site-detail",{title:"Chroot Path",key:"chroot_path",inline:!0}],["wpcloud/site-detail",{title:"Chroot SSH Path",key:"chroot_ssh_path",inline:!0}],["wpcloud/site-detail",{title:"Cache Prefix",key:"cache_prefix",inline:!0}],["wpcloud/site-detail",{title:"Site API Key",key:"site_api_key",inline:!0}]]],["core/column",{verticalAlignment:"top"},[]]]]],o=JSON.parse('{"UU":"wpcloud/site-details"}');(0,e.registerBlockType)(o.UU,{edit:function(){const e=(0,t.useBlockProps)(),o=(0,t.useInnerBlocksProps)(e,{template:l});return(0,i.createElement)("div",{...o,className:"wpcloud-all-site-details"})},save:function(){const e=t.useBlockProps.save();return(0,i.createElement)("div",{...e,className:"wpcloud-block-site-detail-card"},(0,i.createElement)(t.InnerBlocks.Content,null))}})}},t={};function l(e){var o=t[e];if(void 0!==o)return o.exports;var n=t[e]={exports:{}};return i[e](n,n.exports,l),n.exports}l.m=i,e=[],l.O=(i,t,o,n)=>{if(!t){var a=1/0;for(d=0;d<e.length;d++){for(var[t,o,n]=e[d],s=!0,r=0;r<t.length;r++)(!1&n||a>=n)&&Object.keys(l.O).every((e=>l.O[e](t[r])))?t.splice(r--,1):(s=!1,n<a&&(a=n));if(s){e.splice(d--,1);var c=o();void 0!==c&&(i=c)}}return i}n=n||0;for(var d=e.length;d>0&&e[d-1][2]>n;d--)e[d]=e[d-1];e[d]=[t,o,n]},l.o=(e,i)=>Object.prototype.hasOwnProperty.call(e,i),(()=>{var e={842:0,622:0};l.O.j=i=>0===e[i];var i=(i,t)=>{var o,n,[a,s,r]=t,c=0;if(a.some((i=>0!==e[i]))){for(o in s)l.o(s,o)&&(l.m[o]=s[o]);if(r)var d=r(l)}for(i&&i(t);c<a.length;c++)n=a[c],l.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return l.O(d)},t=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];t.forEach(i.bind(null,0)),t.push=i.bind(null,t.push.bind(t))})();var o=l.O(void 0,[622],(()=>l(878)));o=l.O(o)})();