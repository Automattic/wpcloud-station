(()=>{var e,r={294:(e,r,t)=>{"use strict";const o=window.wp.blocks,n=window.React;var s=t(942),l=t.n(s);const a=window.wp.i18n,c=window.wp.blockEditor,i=window.wp.element,u=JSON.parse('{"UU":"wpcloud/site-ssh-users"}');(0,o.registerBlockType)(u.UU,{edit:function(){const e=(0,c.useBlockProps)(),r=(0,i.useMemo)((()=>[["core/group",{className:"wpcloud-domains"},[["core/heading",{level:3,className:"wpcloud-ssh-users__title",content:(0,a.__)("SSH Users")}],["wpcloud/ssh-user-list"],["wpcloud/ssh-user-add"]]]]),[]),t=(0,c.useInnerBlocksProps)(e,{template:r});return(0,n.createElement)("div",{...t,className:l()(t.className,"wpcloud-block-ssh-users")})},save:function(){const e=c.useBlockProps.save();return(0,n.createElement)("div",{...e,className:l()("wpcloud-block-ssh-users",e?.className)},(0,n.createElement)(c.InnerBlocks.Content,null))}})},942:(e,r)=>{var t;!function(){"use strict";var o={}.hasOwnProperty;function n(){for(var e="",r=0;r<arguments.length;r++){var t=arguments[r];t&&(e=l(e,s(t)))}return e}function s(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return n.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var r="";for(var t in e)o.call(e,t)&&e[t]&&(r=l(r,t));return r}function l(e,r){return r?e?e+" "+r:e+r:e}e.exports?(n.default=n,e.exports=n):void 0===(t=function(){return n}.apply(r,[]))||(e.exports=t)}()}},t={};function o(e){var n=t[e];if(void 0!==n)return n.exports;var s=t[e]={exports:{}};return r[e](s,s.exports,o),s.exports}o.m=r,e=[],o.O=(r,t,n,s)=>{if(!t){var l=1/0;for(u=0;u<e.length;u++){for(var[t,n,s]=e[u],a=!0,c=0;c<t.length;c++)(!1&s||l>=s)&&Object.keys(o.O).every((e=>o.O[e](t[c])))?t.splice(c--,1):(a=!1,s<l&&(l=s));if(a){e.splice(u--,1);var i=n();void 0!==i&&(r=i)}}return r}s=s||0;for(var u=e.length;u>0&&e[u-1][2]>s;u--)e[u]=e[u-1];e[u]=[t,n,s]},o.n=e=>{var r=e&&e.__esModule?()=>e.default:()=>e;return o.d(r,{a:r}),r},o.d=(e,r)=>{for(var t in r)o.o(r,t)&&!o.o(e,t)&&Object.defineProperty(e,t,{enumerable:!0,get:r[t]})},o.o=(e,r)=>Object.prototype.hasOwnProperty.call(e,r),(()=>{var e={49:0,877:0};o.O.j=r=>0===e[r];var r=(r,t)=>{var n,s,[l,a,c]=t,i=0;if(l.some((r=>0!==e[r]))){for(n in a)o.o(a,n)&&(o.m[n]=a[n]);if(c)var u=c(o)}for(r&&r(t);i<l.length;i++)s=l[i],o.o(e,s)&&e[s]&&e[s][0](),e[s]=0;return o.O(u)},t=globalThis.webpackChunkwpcloud_station_blocks=globalThis.webpackChunkwpcloud_station_blocks||[];t.forEach(r.bind(null,0)),t.push=r.bind(null,t.push.bind(t))})();var n=o.O(void 0,[877],(()=>o(294)));n=o.O(n)})();