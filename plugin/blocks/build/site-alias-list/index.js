(()=>{var e,r={89:(e,r,t)=>{"use strict";const o=window.wp.blocks,a=window.React;var l=t(942),i=t.n(l);const n=window.wp.i18n,s=window.wp.blockEditor,c=JSON.parse('{"UU":"wpcloud/site-alias-list"}');(0,o.registerBlockType)(c.UU,{edit:function(){const e=(0,s.useBlockProps)(),r=[["core/group",{className:"wpcloud-block-site-alias-list"},[["wpcloud/site-detail",{label:(0,n.__)("Primary Domain"),name:"domain_name",inline:!0,hideLabel:!0,className:"wpcloud-block-site-alias-list__item--primary"}],["wpcloud/form",{ajax:!0,wpcloudAction:"site_alias_remove",inline:!0,className:"wpcloud-block-form-site-alias--remove"},[["wpcloud/site-detail",{label:(0,n.__)("Domain Alias"),name:"site_alias",inline:!0,hideLabel:!0}],["wpcloud/button",{text:(0,n.__)("make primary"),type:"button",className:"wpcloud-block-form-site-alias--make-primary"}],["wpcloud/button",{text:(0,n.__)("remove"),icon:"trash"}]]]]]],t=(0,s.useInnerBlocksProps)(e,{template:r});return(0,a.createElement)("div",{className:"wpcloud-block-site-alias-list--wrapper"},(0,a.createElement)("div",{...t,className:i()(t.className,"wpcloud-block-site-alias-list")}))},save:()=>{const e=s.useBlockProps.save();return(0,a.createElement)("div",{...e,className:i()(e.className,"wpcloud-block-site-alias-list")},(0,a.createElement)(s.InnerBlocks.Content,null))}})},942:(e,r)=>{var t;!function(){"use strict";var o={}.hasOwnProperty;function a(){for(var e="",r=0;r<arguments.length;r++){var t=arguments[r];t&&(e=i(e,l(t)))}return e}function l(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return a.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var r="";for(var t in e)o.call(e,t)&&e[t]&&(r=i(r,t));return r}function i(e,r){return r?e?e+" "+r:e+r:e}e.exports?(a.default=a,e.exports=a):void 0===(t=function(){return a}.apply(r,[]))||(e.exports=t)}()}},t={};function o(e){var a=t[e];if(void 0!==a)return a.exports;var l=t[e]={exports:{}};return r[e](l,l.exports,o),l.exports}o.m=r,e=[],o.O=(r,t,a,l)=>{if(!t){var i=1/0;for(u=0;u<e.length;u++){for(var[t,a,l]=e[u],n=!0,s=0;s<t.length;s++)(!1&l||i>=l)&&Object.keys(o.O).every((e=>o.O[e](t[s])))?t.splice(s--,1):(n=!1,l<i&&(i=l));if(n){e.splice(u--,1);var c=a();void 0!==c&&(r=c)}}return r}l=l||0;for(var u=e.length;u>0&&e[u-1][2]>l;u--)e[u]=e[u-1];e[u]=[t,a,l]},o.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return o.d(r,{a:r}),r},o.d=(e,r)=>{for(var t in r)o.o(r,t)&&!o.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},o.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={659:0,403:0};o.O.j=r=>0===e[r];var r=(r,t)=>{var a,l,[i,n,s]=t,c=0;if(i.some((r=>0!==e[r]))){for(a in n)o.o(n,a)&&(o.m[a]=n[a]);if(s)var u=s(o)}for(r&&r(t);c<i.length;c++)l=i[c],o.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return o.O(u)},t=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var a=o.O(void 0,[403],(()=>o(89)));a=o.O(a)})();