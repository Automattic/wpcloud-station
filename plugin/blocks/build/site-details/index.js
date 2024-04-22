(()=>{"use strict";var e,l={878:()=>{const e=window.wp.blocks,l=window.React,i=(window.wp.i18n,window.wp.blockEditor),a=[["core/columns",{verticalAlignment:"center",isStackedOnMobile:!0,width:"100%"},[["core/column",{verticalAlignment:"top"},[]],["core/column",{verticalAlignment:"top"},[["wpcloud/site-detail",{label:"Domain",name:"domain_name",inline:!0}],["wpcloud/site-detail",{label:"PHP Version",name:"php_version",inline:!0}],["wpcloud/site-detail",{label:"Data Center",name:"geo_affinity",inline:!0}],["wpcloud/site-detail",{label:"WP Version",name:"wp_version",inline:!0}],["wpcloud/site-detail",{label:"Admin Email",name:"wp_admin_email",inline:!0}],["wpcloud/site-detail",{label:"Admin User",name:"wp_admin_user",inline:!0}],["wpcloud/site-detail",{label:"IP Addresses",name:"ip_addresses",inline:!0}],["wpcloud/site-detail",{label:"Static 404",name:"static_file_404",inline:!0}]]],["core/column",{verticalAlignment:"top"},[["wpcloud/site-detail",{label:"DB Charset",name:"db_charset",inline:!0}],["wpcloud/site-detail",{label:"DB Collate",name:"db_collate",inline:!0}],["wpcloud/site-detail",{label:"DB Password",name:"db_password",inline:!0}],["wpcloud/site-detail",{label:"DB File Size",name:"db_file_size",inline:!0}],["wpcloud/site-detail",{label:"SMTP Password",name:"smtp_pass",inline:!0}],["wpcloud/site-detail",{label:"Server Pool ID",name:"server_pool_id",inline:!0}],["wpcloud/site-detail",{label:"Atomic Client ID",name:"atomic_client_id",inline:!0}],["wpcloud/site-detail",{label:"Chroot Path",name:"chroot_path",inline:!0}],["wpcloud/site-detail",{label:"Chroot SSH Path",name:"chroot_ssh_path",inline:!0}],["wpcloud/site-detail",{label:"Cache Prefix",name:"cache_prefix",inline:!0}],["wpcloud/site-detail",{label:"Site API name",name:"site_api_key",inline:!0}]]],["core/column",{verticalAlignment:"top"},[]]]]],n=JSON.parse('{"UU":"wpcloud/site-details"}');(0,e.registerBlockType)(n.UU,{edit:function(){const e=(0,i.useBlockProps)(),n=(0,i.useInnerBlocksProps)(e,{template:a});return(0,l.createElement)("div",{...n,className:"wpcloud-all-site-details"})},save:function(){const e=i.useBlockProps.save();return(0,l.createElement)("div",{...e,className:"wpcloud-block-site-detail-card"},(0,l.createElement)(i.InnerBlocks.Content,null))}})}},i={};function a(e){var n=i[e];if(void 0!==n)return n.exports;var t=i[e]={exports:{}};return l[e](t,t.exports,a),t.exports}a.m=l,e=[],a.O=(l,i,n,t)=>{if(!i){var o=1/0;for(d=0;d<e.length;d++){for(var[i,n,t]=e[d],s=!0,r=0;r<i.length;r++)(!1&t||o>=t)&&Object.keys(a.O).every((e=>a.O[e](i[r])))?i.splice(r--,1):(s=!1,t<o&&(o=t));if(s){e.splice(d--,1);var c=n();void 0!==c&&(l=c)}}return l}t=t||0;for(var d=e.length;d>0&&e[d-1][2]>t;d--)e[d]=e[d-1];e[d]=[i,n,t]},a.o=(e,l)=>Object.prototype.hasOwnProperty.call(e,l),(()=>{var e={842:0,622:0};a.O.j=l=>0===e[l];var l=(l,i)=>{var n,t,[o,s,r]=i,c=0;if(o.some((l=>0!==e[l]))){for(n in s)a.o(s,n)&&(a.m[n]=s[n]);if(r)var d=r(a)}for(l&&l(i);c<o.length;c++)t=o[c],a.o(e,t)&&e[t]&&e[t][0](),e[t]=0;return a.O(d)},i=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];i.forEach(l.bind(null,0)),i.push=l.bind(null,i.push.bind(i))})();var n=a.O(void 0,[622],(()=>a(878)));n=a.O(n)})();