(()=>{var e,r={588:(e,r,t)=>{"use strict";const o=window.wp.blocks,n=window.React;var a=t(942),l=t.n(a);const i=window.wp.i18n,s=window.wp.blockEditor,c=window.wp.element,u=JSON.parse('{"UU":"wpcloud/site-form-add-alias"}');(0,o.registerBlockType)(u.UU,{edit:function(){const e=(0,s.useBlockProps)(),r=(0,c.useMemo)((()=>[["wpcloud/form",{ajax:!0,wpcloudAction:"site_alias_add",inline:!0,className:"wpcloud-block-form--site-alias-add"},[["wpcloud/form-input",{type:"text",label:(0,i.__)("Add a Domain"),name:"site_alias",placeholder:(0,i.__)("new.example.com"),required:!0,inline:!0}],["wpcloud/button",{text:(0,i.__)("Add"),inline:!0}]]]]),[]),t=(0,s.useInnerBlocksProps)(e,{template:r});return(0,n.createElement)("div",{...t,className:l()(t.className,"wpcloud-block-site-alias-add")})},save:()=>{const e=s.useBlockProps.save();return(0,n.createElement)("div",{...e,className:l()(e.className,"wpcloud-block-site-alias-add")},(0,n.createElement)(s.InnerBlocks.Content,null))}})},942:(e,r)=>{var t;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e="",r=0;r<arguments.length;r++){var t=arguments[r];t&&(e=l(e,a(t)))}return e}function a(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return n.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var r="";for(var t in e)o.call(e,t)&&e[t]&&(r=l(r,t));return r}function l(e,r){return r?e?e+" "+r:e+r:e}e.exports?(n.default=n,e.exports=n):void 0===(t=function(){return n}.apply(r,[]))||(e.exports=t)}()}},t={};function o(e){var n=t[e];if(void 0!==n)return n.exports;var a=t[e]={exports:{}};return r[e](a,a.exports,o),a.exports}o.m=r,e=[],o.O=(r,t,n,a)=>{if(!t){var l=1/0;for(u=0;u<e.length;u++){for(var[t,n,a]=e[u],i=!0,s=0;s<t.length;s++)(!1&a||l>=a)&&Object.keys(o.O).every((e=>o.O[e](t[s])))?t.splice(s--,1):(i=!1,a<l&&(l=a));if(i){e.splice(u--,1);var c=n();void 0!==c&&(r=c)}}return r}a=a||0;for(var u=e.length;u>0&&e[u-1][2]>a;u--)e[u]=e[u-1];e[u]=[t,n,a]},o.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return o.d(r,{a:r}),r},o.d=(e,r)=>{for(var t in r)o.o(r,t)&&!o.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},o.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={73:0,245:0};o.O.j=r=>0===e[r];var r=(r,t)=>{var n,a,[l,i,s]=t,c=0;if(l.some((r=>0!==e[r]))){for(n in i)o.o(i,n)&&(o.m[n]=i[n]);if(s)var u=s(o)}for(r&&r(t);c<l.length;c++)a=l[c],o.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return o.O(u)},t=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var n=o.O(void 0,[245],(()=>o(588)));n=o.O(n)})();