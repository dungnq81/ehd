(()=>{var e,t={9351:(e,t,r)=>{"use strict";r.r(t);var l=r(9196),n=r(1984),o=r(2010);const a=window.wp.blocks,c=window.wc.wcSettings;r(805);const s=JSON.parse('{"name":"woocommerce/products-by-attribute","title":"Products by Attribute","category":"woocommerce","keywords":["WooCommerce"],"description":"Display a grid of products with selected attributes.","supports":{"align":["wide","full"],"html":false},"attributes":{"attributes":{"type":"array","default":[]},"attrOperator":{"type":"string","enum":["all","any"],"default":"any"},"columns":{"type":"number","default":3},"contentVisibility":{"type":"object","default":{"image":true,"title":true,"price":true,"rating":true,"button":true},"properties":{"image":{"type":"boolean","default":true},"title":{"type":"boolean","default":true},"price":{"type":"boolean","default":true},"rating":{"type":"boolean","default":true},"button":{"type":"boolean","default":true}}},"orderby":{"type":"string","enum":["date","popularity","price_asc","price_desc","rating","title","menu_order"],"default":"date"},"rows":{"type":"number","default":3},"alignButtons":{"type":"boolean","default":false},"isPreview":{"type":"boolean","default":false},"stockStatus":{"type":"array"}},"textdomain":"woocommerce","apiVersion":2,"$schema":"https://schemas.wp.org/trunk/block.json"}'),i=window.wp.blockEditor,u=window.wp.components;var d=r(9307),m=r(5736);const g=(e,t,r)=>r?Math.min(e,t)===e?t:Math.max(e,r)===e?r:e:Math.max(e,t)===t?e:t,h=({columns:e,rows:t,setAttributes:r,alignButtons:n,minColumns:o=1,maxColumns:a=6,minRows:c=1,maxRows:s=6})=>(0,l.createElement)(l.Fragment,null,(0,l.createElement)(u.RangeControl,{label:(0,m.__)("Columns","woocommerce"),value:e,onChange:e=>{const t=g(e,o,a);r({columns:Number.isNaN(t)?"":t})},min:o,max:a}),(0,l.createElement)(u.RangeControl,{label:(0,m.__)("Rows","woocommerce"),value:t,onChange:e=>{const t=g(e,c,s);r({rows:Number.isNaN(t)?"":t})},min:c,max:s}),(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Align the last block to the bottom","woocommerce"),help:n?(0,m.__)("Align the last block to the bottom.","woocommerce"):(0,m.__)("The last inner block will follow other content.","woocommerce"),checked:n,onChange:()=>r({alignButtons:!n})})),b=({onChange:e,settings:t})=>{const{image:r,button:n,price:o,rating:a,title:c}=t,s=!1!==r;return(0,l.createElement)(l.Fragment,null,(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Product image","woocommerce"),checked:s,onChange:()=>e({...t,image:!s})}),(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Product title","woocommerce"),checked:c,onChange:()=>e({...t,title:!c})}),(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Product price","woocommerce"),checked:o,onChange:()=>e({...t,price:!o})}),(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Product rating","woocommerce"),checked:a,onChange:()=>e({...t,rating:!a})}),(0,l.createElement)(u.ToggleControl,{label:(0,m.__)("Add to Cart button","woocommerce"),checked:n,onChange:()=>e({...t,button:!n})}))};var p=r(4184),E=r.n(p);function w(e,t,r){const l=new Set(t.map((e=>e[r])));return e.filter((e=>!l.has(e[r])))}const _=window.wp.htmlEntities,f={clear:(0,m.__)("Clear all selected items","woocommerce"),noItems:(0,m.__)("No items found.","woocommerce"),
/* Translators: %s search term */
noResults:(0,m.__)("No results for %s","woocommerce"),search:(0,m.__)("Search for items","woocommerce"),selected:e=>(0,m.sprintf)(/* translators: Number of items selected from list. */
(0,m._n)("%d item selected","%d items selected",e,"woocommerce"),e),updated:(0,m.__)("Search results updated.","woocommerce")},y=(e,t=e)=>{const r=e.reduce(((e,t)=>{const r=t.parent||0;return e[r]||(e[r]=[]),e[r].push(t),e}),{}),l=("id",t.reduce(((e,t)=>(e[String(t.id)]=t,e)),{}));const n=["0"],o=(e={})=>e.parent?[...o(l[e.parent]),e.name]:e.name?[e.name]:[],a=e=>e.map((e=>{const t=r[e.id];return n.push(""+e.id),{...e,breadcrumbs:o(l[e.parent]),children:t&&t.length?a(t):[]}})),c=a(r[0]||[]);return Object.entries(r).forEach((([e,t])=>{n.includes(e)||c.push(...a(t||[]))})),c},k=(e,t)=>{if(!t)return e;const r=new RegExp(`(${t.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&")})`,"ig");return e.split(r).map(((e,t)=>r.test(e)?(0,l.createElement)("strong",{key:t},e):(0,l.createElement)(d.Fragment,{key:t},e)))},x=({label:e})=>(0,l.createElement)("span",{className:"woocommerce-search-list__item-count"},e),v=e=>{const{item:t,search:r}=e,n=t.breadcrumbs&&t.breadcrumbs.length;return(0,l.createElement)("span",{className:"woocommerce-search-list__item-label"},n?(0,l.createElement)("span",{className:"woocommerce-search-list__item-prefix"},1===(o=t.breadcrumbs).length?o.slice(0,1).toString():2===o.length?o.slice(0,1).toString()+" › "+o.slice(-1).toString():o.slice(0,1).toString()+" … "+o.slice(-1).toString()):null,(0,l.createElement)("span",{className:"woocommerce-search-list__item-name"},k((0,_.decodeEntities)(t.name),r)));var o},C=({countLabel:e,className:t,depth:r=0,controlId:n="",item:o,isSelected:a,isSingle:c,onSelect:s,search:i="",selected:m,useExpandedPanelId:g,...h})=>{var b,p;const[f,y]=g,C=null!=e&&void 0!==o.count&&null!==o.count,S=!(null===(b=o.breadcrumbs)||void 0===b||!b.length),N=!(null===(p=o.children)||void 0===p||!p.length),O=f===o.id,I=E()(["woocommerce-search-list__item",`depth-${r}`,t],{"has-breadcrumbs":S,"has-children":N,"has-count":C,"is-expanded":O,"is-radio-button":c}),P=h.name||`search-list-item-${n}`,A=`${P}-${o.id}`,B=(0,d.useCallback)((()=>{y(O?-1:Number(o.id))}),[O,o.id,y]);return N?(0,l.createElement)("div",{className:I,onClick:B,onKeyDown:e=>"Enter"===e.key||" "===e.key?B():null,role:"treeitem",tabIndex:0},c?(0,l.createElement)(l.Fragment,null,(0,l.createElement)("input",{type:"radio",id:A,name:P,value:o.value,onChange:s(o),onClick:e=>e.stopPropagation(),checked:a,className:"woocommerce-search-list__item-input",...h}),(0,l.createElement)(v,{item:o,search:i}),C?(0,l.createElement)(x,{label:e||o.count}):null):(0,l.createElement)(l.Fragment,null,(0,l.createElement)(u.CheckboxControl,{className:"woocommerce-search-list__item-input",checked:a,...!a&&o.children.some((e=>m.find((t=>t.id===e.id))))?{indeterminate:!0}:{},label:k((0,_.decodeEntities)(o.name),i),onChange:()=>{a?s(w(m,o.children,"id"))():s(function(e,t,r){const l=w(t,e,"id");return[...e,...l]}(m,o.children))()},onClick:e=>e.stopPropagation()}),C?(0,l.createElement)(x,{label:e||o.count}):null)):(0,l.createElement)("label",{htmlFor:A,className:I},c?(0,l.createElement)(l.Fragment,null,(0,l.createElement)("input",{...h,type:"radio",id:A,name:P,value:o.value,onChange:s(o),checked:a,className:"woocommerce-search-list__item-input"}),(0,l.createElement)(v,{item:o,search:i})):(0,l.createElement)(u.CheckboxControl,{...h,id:A,name:P,className:"woocommerce-search-list__item-input",value:(0,_.decodeEntities)(o.value),label:k((0,_.decodeEntities)(o.name),i),onChange:s(o),checked:a}),C?(0,l.createElement)(x,{label:e||o.count}):null)},S=C;var N=r(5430),O=r(4333),I=r(906);r(5932);const P=({id:e,label:t,popoverContents:r,remove:o,screenReaderLabel:a,className:c=""})=>{const[s,i]=(0,d.useState)(!1),g=(0,O.useInstanceId)(P);if(a=a||t,!t)return null;t=(0,_.decodeEntities)(t);const h=E()("woocommerce-tag",c,{"has-remove":!!o}),b=`woocommerce-tag__label-${g}`,p=(0,l.createElement)(l.Fragment,null,(0,l.createElement)("span",{className:"screen-reader-text"},a),(0,l.createElement)("span",{"aria-hidden":"true"},t));return(0,l.createElement)("span",{className:h},r?(0,l.createElement)(u.Button,{className:"woocommerce-tag__text",id:b,onClick:()=>i(!0)},p):(0,l.createElement)("span",{className:"woocommerce-tag__text",id:b},p),r&&s&&(0,l.createElement)(u.Popover,{onClose:()=>i(!1)},r),o&&(0,l.createElement)(u.Button,{className:"woocommerce-tag__remove",onClick:o(e),label:(0,m.sprintf)(
// Translators: %s label.
(0,m.__)("Remove %s","woocommerce"),t),"aria-describedby":b},(0,l.createElement)(n.Z,{icon:I.Z,size:20,className:"clear-icon",role:"img"})))},A=P;r(8462);const B=e=>(0,l.createElement)(S,{...e}),$=e=>{const{list:t,selected:r,renderItem:n,depth:o=0,onSelect:a,instanceId:c,isSingle:s,search:i,useExpandedPanelId:u}=e,[m]=u;return t?(0,l.createElement)(d.Fragment,null,t.map((t=>{var g,h;const b=null!==(g=t.children)&&void 0!==g&&g.length&&!s?t.children.every((({id:e})=>r.find((t=>t.id===e)))):!!r.find((({id:e})=>e===t.id)),p=(null===(h=t.children)||void 0===h?void 0:h.length)&&m===t.id;return(0,l.createElement)(d.Fragment,{key:t.id},(0,l.createElement)("li",null,n({item:t,isSelected:b,onSelect:a,isSingle:s,selected:r,search:i,depth:o,useExpandedPanelId:u,controlId:c})),p?(0,l.createElement)($,{...e,list:t.children,depth:o+1}):null)}))):null},R=({isLoading:e,isSingle:t,selected:r,messages:n,onChange:o,onRemove:a})=>{if(e||t||!r)return null;const c=r.length;return(0,l.createElement)("div",{className:"woocommerce-search-list__selected"},(0,l.createElement)("div",{className:"woocommerce-search-list__selected-header"},(0,l.createElement)("strong",null,n.selected(c)),c>0?(0,l.createElement)(u.Button,{isLink:!0,isDestructive:!0,onClick:()=>o([]),"aria-label":n.clear},(0,m.__)("Clear all","woocommerce")):null),c>0?(0,l.createElement)("ul",null,r.map(((e,t)=>(0,l.createElement)("li",{key:t},(0,l.createElement)(A,{label:e.name,id:e.id,remove:a}))))):null)},L=({filteredList:e,search:t,onSelect:r,instanceId:o,useExpandedPanelId:a,...c})=>{const{messages:s,renderItem:i,selected:u,isSingle:d}=c,g=i||B;return 0===e.length?(0,l.createElement)("div",{className:"woocommerce-search-list__list is-not-found"},(0,l.createElement)("span",{className:"woocommerce-search-list__not-found-icon"},(0,l.createElement)(n.Z,{icon:N.Z,role:"img"})),(0,l.createElement)("span",{className:"woocommerce-search-list__not-found-text"},t?(0,m.sprintf)(s.noResults,t):s.noItems)):(0,l.createElement)("ul",{className:"woocommerce-search-list__list"},(0,l.createElement)($,{useExpandedPanelId:a,list:e,selected:u,renderItem:g,onSelect:r,instanceId:o,isSingle:d,search:t}))},T=e=>{const{className:t="",isCompact:r,isHierarchical:n,isLoading:o,isSingle:a,list:c,messages:s=f,onChange:i,onSearch:g,selected:h,type:b="text",debouncedSpeak:p}=e,[w,_]=(0,d.useState)(""),k=(0,d.useState)(-1),x=(0,O.useInstanceId)(T),v=(0,d.useMemo)((()=>({...f,...s})),[s]),C=(0,d.useMemo)((()=>((e,t,r)=>{if(!t)return r?y(e):e;const l=new RegExp(t.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&"),"i"),n=e.map((e=>!!l.test(e.name)&&e)).filter(Boolean);return r?y(n,e):n})(c,w,n)),[c,w,n]);(0,d.useEffect)((()=>{p&&p(v.updated)}),[p,v]),(0,d.useEffect)((()=>{"function"==typeof g&&g(w)}),[w,g]);const S=(0,d.useCallback)((e=>()=>{a&&i([]);const t=h.findIndex((({id:t})=>t===e));i([...h.slice(0,t),...h.slice(t+1)])}),[a,h,i]),N=(0,d.useCallback)((e=>()=>{Array.isArray(e)?i(e):-1===h.findIndex((({id:t})=>t===e.id))?i(a?[e]:[...h,e]):S(e.id)()}),[a,S,i,h]),I=(0,d.useCallback)((e=>{const[t]=h.filter((t=>!e.find((e=>t.id===e.id))));S(t.id)()}),[S,h]);return(0,l.createElement)("div",{className:E()("woocommerce-search-list",t,{"is-compact":r,"is-loading":o,"is-token":"token"===b})},"text"===b&&(0,l.createElement)(R,{...e,onRemove:S,messages:v}),(0,l.createElement)("div",{className:"woocommerce-search-list__search"},"text"===b?(0,l.createElement)(u.TextControl,{label:v.search,type:"search",value:w,onChange:e=>_(e)}):(0,l.createElement)(u.FormTokenField,{disabled:o,label:v.search,onChange:I,onInputChange:e=>_(e),suggestions:[],__experimentalValidateInput:()=>!1,value:o?[(0,m.__)("Loading…","woocommerce")]:h.map((e=>({...e,value:e.name}))),__experimentalShowHowTo:!1})),o?(0,l.createElement)("div",{className:"woocommerce-search-list__list"},(0,l.createElement)(u.Spinner,null)):(0,l.createElement)(L,{...e,search:w,filteredList:C,messages:v,onSelect:N,instanceId:x,useExpandedPanelId:k}))},F=((0,u.withSpokenMessages)(T),window.wp.url,window.wp.apiFetch);var j=r.n(F);const M=e=>j()({path:`wc/store/v1/products/attributes/${e}/terms`});const Z=window.wp.escapeHtml,D=({error:e})=>(0,l.createElement)("div",{className:"wc-block-error-message"},(({message:e,type:t})=>e?"general"===t?(0,l.createElement)("span",null,(0,m.__)("The following error was returned","woocommerce"),(0,l.createElement)("br",null),(0,l.createElement)("code",null,(0,Z.escapeHTML)(e))):"api"===t?(0,l.createElement)("span",null,(0,m.__)("The following error was returned from the API","woocommerce"),(0,l.createElement)("br",null),(0,l.createElement)("code",null,(0,Z.escapeHTML)(e))):e:(0,m.__)("An error has prevented the block from being updated.","woocommerce"))(e)),H=({className:e,item:t,isSelected:r,isLoading:n,onSelect:o,disabled:a,...c})=>(0,l.createElement)(l.Fragment,null,(0,l.createElement)(C,{...c,key:t.id,className:e,isSelected:r,item:t,onSelect:o,disabled:a}),r&&n&&(0,l.createElement)("div",{key:"loading",className:E()("woocommerce-search-list__item","woocommerce-product-attributes__item","depth-1","is-loading","is-not-active")},(0,l.createElement)(u.Spinner,null)));function V(e,t){return!(e=>null===e)(r=e)&&r instanceof Object&&r.constructor===Object&&t in e;var r}const J=((window.wp.data,(0,c.getSetting)("attributes",[])).reduce(((e,t)=>{const r=(l=t)&&l.attribute_name?{id:parseInt(l.attribute_id,10),name:l.attribute_name,taxonomy:"pa_"+l.attribute_name,label:l.attribute_label}:null;var l;return r&&r.id&&e.push(r),e}),[]),e=>{const{count:t,id:r,name:l,parent:n}=e;return{count:t,id:r,name:l,parent:n,breadcrumbs:[],children:[],value:(o=e,V(o,"count")&&V(o,"description")&&V(o,"id")&&V(o,"name")&&V(o,"parent")&&V(o,"slug")&&"number"==typeof o.count&&"string"==typeof o.description&&"number"==typeof o.id&&"string"==typeof o.name&&"number"==typeof o.parent&&"string"==typeof o.slug?e.attr_slug:"")};var o});r(9669);const W=(0,O.withInstanceId)((({onChange:e,onOperatorChange:t,instanceId:r,isCompact:n=!1,messages:o={},operator:a="any",selected:c,type:s="text"})=>{const{errorLoadingAttributes:i,isLoadingAttributes:g,productsAttributes:h}=function(e){const[t,r]=(0,d.useState)(null),[l,n]=(0,d.useState)(!1),[o,a]=(0,d.useState)([]),c=(0,d.useRef)(!1);return(0,d.useEffect)((()=>{if(e&&!l&&!c.current)return async function(){n(!0);try{const e=await j()({path:"wc/store/v1/products/attributes"}),t=[];for(const r of e){const e=await M(r.id);t.push({...r,parent:0,terms:e.map((e=>({...e,attr_slug:r.taxonomy,parent:r.id})))})}a(t),c.current=!0}catch(e){e instanceof Error&&r(await(async e=>{if(!("json"in e))return{message:e.message,type:e.type||"general"};try{const t=await e.json();return{message:t.message,type:t.type||"api"}}catch(e){return{message:e.message,type:"general"}}})(e))}finally{n(!1)}}(),()=>{c.current=!0}}),[l,e]),{errorLoadingAttributes:t,isLoadingAttributes:l,productsAttributes:o}}(!0),b=h.reduce(((e,t)=>{const{terms:r,...l}=t;return[...e,J(l),...r.map(J)]}),[]);return o={clear:(0,m.__)("Clear all product attributes","woocommerce"),noItems:(0,m.__)("Your store doesn't have any product attributes.","woocommerce"),search:(0,m.__)("Search for product attributes","woocommerce"),selected:e=>(0,m.sprintf)(/* translators: %d is the count of attributes selected. */
(0,m._n)("%d attribute selected","%d attributes selected",e,"woocommerce"),e),updated:(0,m.__)("Product attribute search results updated.","woocommerce"),...o},i?(0,l.createElement)(D,{error:i}):(0,l.createElement)(l.Fragment,null,(0,l.createElement)(T,{className:"woocommerce-product-attributes",isCompact:n,isHierarchical:!0,isLoading:g,isSingle:!1,list:b,messages:o,onChange:e,renderItem:e=>{const{item:t,search:n,depth:o=0}=e,a=t.count||0,c=["woocommerce-product-attributes__item","woocommerce-search-list__item",{"is-searching":n.length>0,"is-skip-level":0===o&&0!==t.parent}];if(!t.breadcrumbs.length)return(0,l.createElement)(H,{...e,className:E()(c),item:t,isLoading:g,disabled:0===t.count,name:`attributes-${r}`,countLabel:(0,m.sprintf)(/* translators: %d is the count of terms. */
(0,m._n)("%d term","%d terms",a,"woocommerce"),a),"aria-label":(0,m.sprintf)(/* translators: %1$s is the item name, %2$d is the count of terms for the item. */
(0,m._n)("%1$s, has %2$d term","%1$s, has %2$d terms",a,"woocommerce"),t.name,a)});const s=`${t.breadcrumbs[0]}: ${t.name}`;return(0,l.createElement)(C,{...e,name:`terms-${r}`,className:E()(...c,"has-count"),countLabel:(0,m.sprintf)(/* translators: %d is the count of products. */
(0,m._n)("%d product","%d products",a,"woocommerce"),a),"aria-label":(0,m.sprintf)(/* translators: %1$s is the attribute name, %2$d is the count of products for that attribute. */
(0,m._n)("%1$s, has %2$d product","%1$s, has %2$d products",a,"woocommerce"),s,a)})},selected:c.map((({id:e})=>b.find((t=>t.id===e)))).filter(Boolean),type:s}),!!t&&(0,l.createElement)("div",{hidden:c.length<2},(0,l.createElement)(u.SelectControl,{className:"woocommerce-product-attributes__operator",label:(0,m.__)("Display products matching","woocommerce"),help:(0,m.__)("Pick at least two attributes to use this setting.","woocommerce"),value:a,onChange:t,options:[{label:(0,m.__)("Any selected attributes","woocommerce"),value:"any"},{label:(0,m.__)("All selected attributes","woocommerce"),value:"all"}]})))})),G=({value:e,setAttributes:t})=>(0,l.createElement)(u.SelectControl,{label:(0,m.__)("Order products by","woocommerce"),value:e,options:[{label:(0,m.__)("Newness - newest first","woocommerce"),value:"date"},{label:(0,m.__)("Price - low to high","woocommerce"),value:"price_asc"},{label:(0,m.__)("Price - high to low","woocommerce"),value:"price_desc"},{label:(0,m.__)("Rating - highest first","woocommerce"),value:"rating"},{label:(0,m.__)("Sales - most first","woocommerce"),value:"popularity"},{label:(0,m.__)("Title - alphabetical","woocommerce"),value:"title"},{label:(0,m.__)("Menu Order","woocommerce"),value:"menu_order"}],onChange:e=>t({orderby:e})}),z=(0,c.getSetting)("hideOutOfStockItems",!1),K=(0,c.getSetting)("stockStatusOptions",{}),Y=({value:e,setAttributes:t})=>{const{outofstock:r,...n}=K,o=z?n:K,a=Object.entries(o).map((([e,t])=>({value:e,label:t}))).filter((e=>!!e.label)),c=Object.keys(o).filter((e=>!!e)),[s,i]=(0,d.useState)(e||c);(0,d.useEffect)((()=>{t({stockStatus:["",...s]})}),[s,t]);const g=(0,d.useCallback)((e=>{const t=s.includes(e),r=s.filter((t=>t!==e));t||(r.push(e),r.sort()),i(r)}),[s]);return(0,l.createElement)(l.Fragment,null,a.map((e=>{const t=s.includes(e.value)?/* translators: %s stock status. */(0,m.__)('Stock status "%s" visible.',"woocommerce"):/* translators: %s stock status. */(0,m.__)('Stock status "%s" hidden.',"woocommerce");return(0,l.createElement)(u.ToggleControl,{label:e.label,key:e.value,help:(0,m.sprintf)(t,e.label),checked:s.includes(e.value),onChange:()=>g(e.value)})})))},q=e=>{const{setAttributes:t}=e,{attributes:r,attrOperator:n,columns:o,contentVisibility:a,orderby:s,rows:d,alignButtons:g,stockStatus:p}=e.attributes;return(0,l.createElement)(i.InspectorControls,{key:"inspector"},(0,l.createElement)(u.PanelBody,{title:(0,m.__)("Layout","woocommerce"),initialOpen:!0},(0,l.createElement)(h,{columns:o,rows:d,alignButtons:g,setAttributes:t,minColumns:(0,c.getSetting)("minColumns",1),maxColumns:(0,c.getSetting)("maxColumns",6),minRows:(0,c.getSetting)("minRows",1),maxRows:(0,c.getSetting)("maxRows",6)})),(0,l.createElement)(u.PanelBody,{title:(0,m.__)("Content","woocommerce"),initialOpen:!0},(0,l.createElement)(b,{settings:a,onChange:e=>t({contentVisibility:e})})),(0,l.createElement)(u.PanelBody,{title:(0,m.__)("Filter by Product Attribute","woocommerce"),initialOpen:!1},(0,l.createElement)(W,{selected:r,onChange:(e=[])=>{const r=e.map((({id:e,attr_slug:t})=>({id:e,attr_slug:t})));t({attributes:r})},operator:n,onOperatorChange:(e="any")=>t({attrOperator:e}),isCompact:!0})),(0,l.createElement)(u.PanelBody,{title:(0,m.__)("Order By","woocommerce"),initialOpen:!1},(0,l.createElement)(G,{setAttributes:t,value:s})),(0,l.createElement)(u.PanelBody,{title:(0,m.__)("Filter by stock status","woocommerce"),initialOpen:!1},(0,l.createElement)(Y,{setAttributes:t,value:p})))},Q=e=>{const{attributes:t,setAttributes:r,setIsEditing:a,isEditing:c,debouncedSpeak:s}=e;return(0,l.createElement)(u.Placeholder,{icon:(0,l.createElement)(n.Z,{icon:o.Z}),label:(0,m.__)("Products by Attribute","woocommerce"),className:"wc-block-products-grid wc-block-products-by-attribute"},(0,m.__)("Display a grid of products from your selected attributes.","woocommerce"),(0,l.createElement)("div",{className:"wc-block-products-by-attribute__selection"},(0,l.createElement)(W,{selected:t.attributes,onChange:(e=[])=>{const t=e.map((({id:e,value:t})=>({id:e,attr_slug:t})));r({attributes:t})},operator:t.attrOperator,onOperatorChange:(e="any")=>r({attrOperator:e})}),(0,l.createElement)(u.Button,{isPrimary:!0,onClick:()=>{a(!c),s((0,m.__)("Showing Products by Attribute block preview.","woocommerce"))}},(0,m.__)("Done","woocommerce"))))},U=window.wp.serverSideRender;var X=r.n(U);const ee=(0,l.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 230 250",style:{width:"100%"}},(0,l.createElement)("title",null,"Grid Block Preview"),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:".162",y:".779",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"9.216",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"1.565",y:"101.448",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:".162",y:"136.277",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"9.216",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"1.565",y:"236.946",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:"82.478",y:".779",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"91.532",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"83.882",y:"101.448",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:"82.478",y:"136.277",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"91.532",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"83.882",y:"236.946",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:"164.788",y:".779",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"173.843",y:"76.153",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"166.192",y:"101.448",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"65.374",height:"65.374",x:"164.788",y:"136.277",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"47.266",height:"5.148",x:"173.843",y:"211.651",fill:"#E1E3E6",rx:"2.574"}),(0,l.createElement)("rect",{width:"62.8",height:"15",x:"166.192",y:"236.946",fill:"#E1E3E6",rx:"5"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"13.283",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"21.498",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"29.713",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"37.927",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"46.238",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"95.599",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"103.814",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"112.029",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"120.243",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"128.554",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"177.909",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"186.124",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"194.339",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"202.553",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"210.864",y:"86.301",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"13.283",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"21.498",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"29.713",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"37.927",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"46.238",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"95.599",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"103.814",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"112.029",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"120.243",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"128.554",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"177.909",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"186.124",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"194.339",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"202.553",y:"221.798",fill:"#E1E3E6",rx:"3"}),(0,l.createElement)("rect",{width:"6.177",height:"6.177",x:"210.864",y:"221.798",fill:"#E1E3E6",rx:"3"})),te=e=>{const{attributes:t,name:r}=e;return t.isPreview?ee:(0,l.createElement)(X(),{block:r,attributes:t})},re=(0,u.withSpokenMessages)((e=>{const t=(0,i.useBlockProps)(),{attributes:{attributes:r}}=e,[n,o]=(0,d.useState)(!r.length);return(0,l.createElement)("div",{...t},(0,l.createElement)(i.BlockControls,null,(0,l.createElement)(u.ToolbarGroup,{controls:[{icon:"edit",title:(0,m.__)("Edit selected attribute","woocommerce"),onClick:()=>o(!n),isActive:n}]})),(0,l.createElement)(q,{...e}),n?(0,l.createElement)(Q,{isEditing:n,setIsEditing:o,...e}):(0,l.createElement)(u.Disabled,null,(0,l.createElement)(te,{...e})))}));(0,a.registerBlockType)(s,{icon:{src:(0,l.createElement)(n.Z,{icon:o.Z,className:"wc-block-editor-components-block-icon"})},attributes:{...s.attributes,columns:{type:"number",default:(0,c.getSetting)("defaultColumns",3)},rows:{type:"number",default:(0,c.getSetting)("defaultRows",3)},stockStatus:{type:"array",default:Object.keys((0,c.getSetting)("stockStatusOptions",[]))}},edit:re,save:()=>null})},805:()=>{},9669:()=>{},8462:()=>{},5932:()=>{},9196:e=>{"use strict";e.exports=window.React},4333:e=>{"use strict";e.exports=window.wp.compose},9307:e=>{"use strict";e.exports=window.wp.element},5736:e=>{"use strict";e.exports=window.wp.i18n},444:e=>{"use strict";e.exports=window.wp.primitives}},r={};function l(e){var n=r[e];if(void 0!==n)return n.exports;var o=r[e]={exports:{}};return t[e].call(o.exports,o,o.exports,l),o.exports}l.m=t,e=[],l.O=(t,r,n,o)=>{if(!r){var a=1/0;for(u=0;u<e.length;u++){for(var[r,n,o]=e[u],c=!0,s=0;s<r.length;s++)(!1&o||a>=o)&&Object.keys(l.O).every((e=>l.O[e](r[s])))?r.splice(s--,1):(c=!1,o<a&&(a=o));if(c){e.splice(u--,1);var i=n();void 0!==i&&(t=i)}}return t}o=o||0;for(var u=e.length;u>0&&e[u-1][2]>o;u--)e[u]=e[u-1];e[u]=[r,n,o]},l.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return l.d(t,{a:t}),t},l.d=(e,t)=>{for(var r in t)l.o(t,r)&&!l.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),l.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},l.j=4341,(()=>{var e={4341:0};l.O.j=t=>0===e[t];var t=(t,r)=>{var n,o,[a,c,s]=r,i=0;if(a.some((t=>0!==e[t]))){for(n in c)l.o(c,n)&&(l.m[n]=c[n]);if(s)var u=s(l)}for(t&&t(r);i<a.length;i++)o=a[i],l.o(e,o)&&e[o]&&e[o][0](),e[o]=0;return l.O(u)},r=self.webpackChunkwebpackWcBlocksJsonp=self.webpackChunkwebpackWcBlocksJsonp||[];r.forEach(t.bind(null,0)),r.push=t.bind(null,r.push.bind(r))})();var n=l.O(void 0,[2869],(()=>l(9351)));n=l.O(n),((this.wc=this.wc||{}).blocks=this.wc.blocks||{})["products-by-attribute"]=n})();