(()=>{var e,r={125:(e,r,t)=>{"use strict";const n=window.wp.blocks,o=window.React;var l=t(942),i=t.n(l);const a=window.wp.i18n,s=window.wp.blockEditor,c=window.wp.components,d=window.wp.data,u=JSON.parse('{"UU":"wpcloud/site-detail-card"}');(0,n.registerBlockType)(u.UU,{edit:function({attributes:e,setAttributes:r,clientId:t,isSelected:n}){const{adminOnly:l}=e,u=(0,s.useBlockProps)(),p=(0,d.useSelect)((e=>e("core/block-editor").hasSelectedInnerBlock(t))),f=(0,s.useInnerBlocksProps)(u,{renderAppender:n||p?s.InnerBlocks.ButtonBlockAppender:void 0});return(0,o.createElement)(o.Fragment,null,(0,o.createElement)(s.InspectorControls,null,(0,o.createElement)(c.PanelBody,{title:(0,a.__)("Form Settings")},(0,o.createElement)(c.CheckboxControl,{label:(0,a.__)("Limit to Admins"),checked:l,onChange:e=>{r({adminOnly:e})},help:(0,a.__)("Only admins will see this field. Inputs marked as admin only will appear with a dashed border in the editor")}))),(0,o.createElement)("div",{...f,className:i()("wpcloud-block-site-detail-card",{"is-admin-only":l})}))},save:function(){const e=s.useBlockProps.save();return(0,o.createElement)("div",{...e,className:"wpcloud-block-site-detail-card"},(0,o.createElement)(s.InnerBlocks.Content,null))}})},942:(e,r)=>{var t;!function(){"use strict";var n={}.hasOwnProperty;function o(){for(var e="",r=0;r<arguments.length;r++){var t=arguments[r];t&&(e=i(e,l(t)))}return e}function l(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return o.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var r="";for(var t in e)n.call(e,t)&&e[t]&&(r=i(r,t));return r}function i(e,r){return r?e?e+" "+r:e+r:e}e.exports?(o.default=o,e.exports=o):void 0===(t=function(){return o}.apply(r,[]))||(e.exports=t)}()}},t={};function n(e){var o=t[e];if(void 0!==o)return o.exports;var l=t[e]={exports:{}};return r[e](l,l.exports,n),l.exports}n.m=r,e=[],n.O=(r,t,o,l)=>{if(!t){var i=1/0;for(d=0;d<e.length;d++){for(var[t,o,l]=e[d],a=!0,s=0;s<t.length;s++)(!1&l||i>=l)&&Object.keys(n.O).every((e=>n.O[e](t[s])))?t.splice(s--,1):(a=!1,l<i&&(i=l));if(a){e.splice(d--,1);var c=o();void 0!==c&&(r=c)}}return r}l=l||0;for(var d=e.length;d>0&&e[d-1][2]>l;d--)e[d]=e[d-1];e[d]=[t,o,l]},n.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return n.d(r,{a:r}),r},n.d=(e,r)=>{for(var t in r)n.o(r,t)&&!n.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},n.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={650:0,934:0};n.O.j=r=>0===e[r];var r=(r,t)=>{var o,l,[i,a,s]=t,c=0;if(i.some((r=>0!==e[r]))){for(o in a)n.o(a,o)&&(n.m[o]=a[o]);if(s)var d=s(n)}for(r&&r(t);c<i.length;c++)l=i[c],n.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return n.O(d)},t=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var o=n.O(void 0,[934],(()=>n(125)));o=n.O(o)})();