(()=>{var e,t={743:(e,t,o)=>{"use strict";const r=window.wp.i18n,l=window.wp.blocks,n=window.React;var c=o(942),i=o.n(c);const a=window.wp.blockEditor,s=JSON.parse('{"UU":"wpcloud/site-template-header"}');(0,l.registerBlockVariation)("core/list",{name:"wpcloud/site-list-columns",title:"Site List Header",description:"Displays the header for a list of WP Cloud Sites",isActive:({namespace:e})=>"wpcloud/site-list--header"===e,attributes:{namespace:"wpcloud/site-list--header",className:"wpcloud-site-list--header"},innerBlocks:[["core/list-item",{content:(0,r.__)("Site","wpcloud")}],["core/list-item",{content:(0,r.__)("Owner","wpcloud")}],["core/list-item",{content:(0,r.__)("Created","wpcloud")}],["core/list-item",{content:(0,r.__)("PHP","wpcloud")}],["core/list-item",{content:(0,r.__)("WP Version","wpcloud")}],["core/list-item",{content:(0,r.__)("IP","wpcloud")}],["core/list-item",{content:(0,r.__)("Actions","wpcloud")}]]}),(0,l.registerBlockType)(s.UU,{edit:function(){const e=(0,a.useBlockProps)(),t=[["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("Site","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("Owner","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("Created","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("PHP","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("WP Version","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("IP","wpcloud")}]]],["wpcloud/table-cell",{isHeader:!0},[["core/heading",{level:2,content:(0,r.__)("Actions","wpcloud")}]]]],o=(0,a.useInnerBlocksProps)(e,{template:t});return(0,n.createElement)("span",{...o,className:i()(o.className,"wpcloud-block-site-list--header")})},save:()=>{const e=a.useBlockProps.save();return(0,n.createElement)("span",{...e,className:i()(e.className,"wpcloud-block-site-list--header")},(0,n.createElement)(a.InnerBlocks.Content,null))}})},942:(e,t)=>{var o;!function(){"use strict";var r={}.hasOwnProperty;function l(){for(var e="",t=0;t<arguments.length;t++){var o=arguments[t];o&&(e=c(e,n(o)))}return e}function n(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return l.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var o in e)r.call(e,o)&&e[o]&&(t=c(t,o));return t}function c(e,t){return t?e?e+" "+t:e+t:e}e.exports?(l.default=l,e.exports=l):void 0===(o=function(){return l}.apply(t,[]))||(e.exports=o)}()}},o={};function r(e){var l=o[e];if(void 0!==l)return l.exports;var n=o[e]={exports:{}};return t[e](n,n.exports,r),n.exports}r.m=t,e=[],r.O=(t,o,l,n)=>{if(!o){var c=1/0;for(d=0;d<e.length;d++){for(var[o,l,n]=e[d],i=!0,a=0;a<o.length;a++)(!1&n||c>=n)&&Object.keys(r.O).every((e=>r.O[e](o[a])))?o.splice(a--,1):(i=!1,n<c&&(c=n));if(i){e.splice(d--,1);var s=l();void 0!==s&&(t=s)}}return t}n=n||0;for(var d=e.length;d>0&&e[d-1][2]>n;d--)e[d]=e[d-1];e[d]=[o,l,n]},r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t},r.d=(e,t)=>{for(var o in t)r.o(t,o)&&!r.o(e,o)&&Object.defineProperty(e,o,{enumerable:!0,get:t[o]})},r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={669:0,353:0};r.O.j=t=>0===e[t];var t=(t,o)=>{var l,n,[c,i,a]=o,s=0;if(c.some((t=>0!==e[t]))){for(l in i)r.o(i,l)&&(r.m[l]=i[l]);if(a)var d=a(r)}for(t&&t(o);s<c.length;s++)n=c[s],r.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return r.O(d)},o=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];o.forEach(t.bind(null,0)),o.push=t.bind(null,o.push.bind(o))})();var l=r.O(void 0,[353],(()=>r(743)));l=r.O(l)})();