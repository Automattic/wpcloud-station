(()=>{var e,t={308:(e,t,r)=>{"use strict";const o=window.wp.blocks,n=window.React;var l=r(942),a=r.n(l);const c=window.wp.i18n,i=window.wp.blockEditor,s=JSON.parse('{"UU":"wpcloud/site-delete"}'),p=window.wp.components,u=(e="")=>{const t=e.replace(/–|-|_/g," ");return t.includes(" ")?t.replace(/\b\w/g,(e=>e.toUpperCase())).replace(/\b(.{2})\b/i,(e=>e.toUpperCase())).replace(/\bapi\b/i,"API").replace(/\bphp\b/i,"PHP"):e};function d({attributes:e,setAttributes:t,onChange:r}){const o=window.wpcloud?.siteDetailKeys||[],l=["-"].concat(o),{name:a}=e;return(0,n.createElement)(p.SelectControl,{label:(0,c.__)("Select a site detail"),value:a,options:l.map((e=>({value:e,label:u(e)}))),onChange:e=>{t({name:e,label:u(e)}),r&&r(e)}})}const w=wp.compose.createHigherOrderComponent((e=>t=>{const{isSelected:r,name:o}=t;return(0,n.createElement)(n.Fragment,null,(0,n.createElement)(e,{...t}),r&&(e=>"wpcloud/form-input"==e)(o)&&(0,n.createElement)(i.InspectorControls,null,(0,n.createElement)(p.PanelBody,{title:(0,c.__)("WP Cloud Site")},"wpcloud/form-input"==o&&(0,n.createElement)(d,{...t}))))}),"wpcloudSiteControls");wp.hooks.addFilter("editor.BlockEdit","wpcloud/site-controls",w),(0,o.registerBlockType)(s.UU,{edit:function(){const e=(0,i.useBlockProps)(),t=[["wpcloud/form",{ajax:!0,wpcloudAction:"site_delete"},[["wpcloud/button",{className:"wpcloud-button-site-delete",text:(0,c.__)("Delete Site")}]]]],r=(0,i.useInnerBlocksProps)(e,{template:t});return(0,n.createElement)("div",{...r,className:a()("wpcloud-block-site-delete",r?.className)})},save:function(){const e=i.useBlockProps.save();return(0,n.createElement)("div",{...e,className:a()("wpcloud-block-site-delete",e?.className)},(0,n.createElement)(i.InnerBlocks.Content,null))}})},942:(e,t)=>{var r;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e="",t=0;t<arguments.length;t++){var r=arguments[t];r&&(e=a(e,l(r)))}return e}function l(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return n.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var r in e)o.call(e,r)&&e[r]&&(t=a(t,r));return t}function a(e,t){return t?e?e+" "+t:e+t:e}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()}},r={};function o(e){var n=r[e];if(void 0!==n)return n.exports;var l=r[e]={exports:{}};return t[e](l,l.exports,o),l.exports}o.m=t,e=[],o.O=(t,r,n,l)=>{if(!r){var a=1/0;for(p=0;p<e.length;p++){for(var[r,n,l]=e[p],c=!0,i=0;i<r.length;i++)(!1&l||a>=l)&&Object.keys(o.O).every((e=>o.O[e](r[i])))?r.splice(i--,1):(c=!1,l<a&&(a=l));if(c){e.splice(p--,1);var s=n();void 0!==s&&(t=s)}}return t}l=l||0;for(var p=e.length;p>0&&e[p-1][2]>l;p--)e[p]=e[p-1];e[p]=[r,n,l]},o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={11:0,459:0};o.O.j=t=>0===e[t];var t=(t,r)=>{var n,l,[a,c,i]=r,s=0;if(a.some((t=>0!==e[t]))){for(n in c)o.o(c,n)&&(o.m[n]=c[n]);if(i)var p=i(o)}for(t&&t(r);s<a.length;s++)l=a[s],o.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return o.O(p)},r=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=o.O(void 0,[459],(()=>o(308)));n=o.O(n)})();