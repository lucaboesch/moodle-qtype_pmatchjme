YUI.add("moodle-qtype_pmatchjme-jsme",function(e,t){M.qtype_pmatchjme=M.qtype_pmatchjme||{},M.qtype_pmatchjme.jsme={reload_limit:10,editor_displayed:!1,topnode:null,applet_name:null,insert_applet:function(t,n,r,i,s,o,u,a,f){var l=["jme",e.one(i+" input.jme").get("value")],c=[];this.applet_name=r,this.topnode=i,a&&(c[c.length]="nostereo"),f&&(c[c.length]="autoez"),u&&(c[c.length]="depict"),c.length!==0&&(l[l.length]="options",l[l.length]=c.join(",")),this.show_java(t,n,r,s,288,312,"JME.class",l,c)},update_inputs:function(){var t=this.applet_name,n=this.topnode;if(!this.editor_displayed)this.show_error(n);else{var r=e.one(n);r.ancestor("form").on("submit",function(){e.one(n+" input.answer").set("value",this.find_applet(t).smiles()),e.one(n+" input.jme").set("value",this.find_applet(t).jmeFile()),e.one(n+" input.mol").set("value",this.find_applet(t).molFile())},this)}},show_error:function(t){var n='<span class ="javascriptwarning">'+M.util.get_string("enablejavascript","qtype_pmatchjme")+"</span>";e.one(t+" .ablock").insert(n,1)},find_applet:function(e){var t=document.jsapplets;for(var n=0;i<t.length;n++)if(t[n].name===e)return t[n];return null},show_java:function(e,t,n,r,i,s,o,u,a){var f=document.getElementById(e);f.innerHTML="";if(typeof JSApplet!="object"&&this.reload_limit)return setTimeout(function(){M.qtype_pmatchjme.jsme.show_java(e,t,n,r,i,s,o,u,a)},100),this.reload_limit--,!1;var l=document.createElement("div");l.setAttribute("code",o),l.setAttribute("archive",r),l.setAttribute("name",n),l.setAttribute("width",i),l.setAttribute("height",s),l.setAttribute("tabIndex",-1),l.setAttribute("mayScript",!0),l.setAttribute("id",t),u[u.length]="focushackid",u[u.length]=l.id;for(var c=0;c<u.length;c+=2){var h=document.createElement("param");h.name=u[c],h.value=u[c+1],l.appendChild(h)}return f.appendChild(l),jsmeApplet=new JSApplet.JSME(t,i+80+"px",s+"px",{options:a.join(",")}),jsmeApplet.name=n,u[1]&&jsmeApplet.readMolecule(u[1]),document.jsapplets||(document.jsapplets=[]),document.jsapplets[document.jsapplets.length]=jsmeApplet,this.editor_displayed=!0,this.update_inputs(),this.editor_displayed}}},"@VERSION@",{requires:["node","event"]});
