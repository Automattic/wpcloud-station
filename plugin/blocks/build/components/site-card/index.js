(()=>{var e,t={535:(e,t,r)=>{"use strict";const a=window.wp.blocks,n=window.wp.blockEditor,o=window.React;var l=r(942),c=r.n(l);const s=window.wp.i18n,i=window.wp.components,u=JSON.parse('{"UU":"wpcloud/site-card"}');(0,a.registerBlockType)(u.UU,{edit:function({attributes:e,className:t}){const{placeholderThumbnail:r}=e;return(0,o.createElement)("div",{...(0,n.useBlockProps)(),className:c()(n.useBlockProps.className,"wp-block-wpcloud-site-card",t)},(0,o.createElement)("img",{src:r}),(0,o.createElement)("h2",{className:"site-title"},(0,o.createElement)("a",{href:"#"},(0,s.__)("Site Name","site-card"))),(0,o.createElement)("h3",{className:"site-url"},(0,o.createElement)("a",{href:"#",target:"_blank"},(0,o.createElement)("span",null,(0,s.__)("Site Domain","site-card")),(0,o.createElement)(i.Dashicon,{icon:"external"}))))},save:function({attributes:e,className:t}){const{placeholderThumbnail:r}=e,a=n.useBlockProps.save();return(0,o.createElement)("div",{...a,className:c()(a.className,"wp-block-wpcloud-site-card",t)},(0,o.createElement)("img",{src:r}),(0,o.createElement)("h2",{className:"site-title"},(0,o.createElement)("a",{href:"#"},(0,s.__)("Site Name","site-card"))),(0,o.createElement)("h3",{className:"site-url"},(0,o.createElement)("a",{href:"#",target:"_blank",rel:"noopener"},(0,o.createElement)("span",null,(0,s.__)("Site Domain","site-card")),(0,o.createElement)(i.Dashicon,{icon:"external"}))))}})},942:(e,t)=>{var r;!function(){"use strict";var a={}.hasOwnProperty;function n(){for(var e="",t=0;t<arguments.length;t++){var r=arguments[t];r&&(e=l(e,o(r)))}return e}function o(e){if("string"==typeof e||"number"==typeof e)return e;if("object"!=typeof e)return"";if(Array.isArray(e))return n.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var t="";for(var r in e)a.call(e,r)&&e[r]&&(t=l(t,r));return t}function l(e,t){return t?e?e+" "+t:e+t:e}e.exports?(n.default=n,e.exports=n):void 0===(r=function(){return n}.apply(t,[]))||(e.exports=r)}()}},r={};function a(e){var n=r[e];if(void 0!==n)return n.exports;var o=r[e]={exports:{}};return t[e](o,o.exports,a),o.exports}a.m=t,e=[],a.O=(t,r,n,o)=>{if(!r){var l=1/0;for(u=0;u<e.length;u++){for(var[r,n,o]=e[u],c=!0,s=0;s<r.length;s++)(!1&o||l>=o)&&Object.keys(a.O).every((e=>a.O[e](r[s])))?r.splice(s--,1):(c=!1,o<l&&(l=o));if(c){e.splice(u--,1);var i=n();void 0!==i&&(t=i)}}return t}o=o||0;for(var u=e.length;u>0&&e[u-1][2]>o;u--)e[u]=e[u-1];e[u]=[r,n,o]},a.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return a.d(t,{a:t}),t},a.d=(e,t)=>{for(var r in t)a.o(t,r)&&!a.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},a.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={207:0,919:0};a.O.j=t=>0===e[t];var t=(t,r)=>{var n,o,[l,c,s]=r,i=0;if(l.some((t=>0!==e[t]))){for(n in c)a.o(c,n)&&(a.m[n]=c[n]);if(s)var u=s(a)}for(t&&t(r);i<l.length;i++)o=l[i],a.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return a.O(u)},r=globalThis.webpackChunkwp_cloud_dashboard_blocks=globalThis.webpackChunkwp_cloud_dashboard_blocks||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=a.O(void 0,[919],(()=>a(535)));n=a.O(n)})();