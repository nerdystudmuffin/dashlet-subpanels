/*
 Copyright (c) 2003 Jan-Klaas Kollhof
 
 This file is part of the JavaScript o lait library(jsolait).
 
 jsolait is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 2.1 of the License, or
 (at your option) any later version.
 
 This software is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Lesser General Public License for more details.
 
 You should have received a copy of the GNU Lesser General Public License
 along with this software; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
Module("urllib","1.1.3",function(mod){mod.NoHTTPRequestObject=Class("NoHTTPRequestObject",mod.Exception,function(publ,supr){publ.init=function(trace){supr(this).init("Could not create an HTTP request object",trace);}})
mod.RequestOpenFailed=Class("RequestOpenFailed",mod.Exception,function(publ,supr){publ.init=function(trace){supr(this).init("Opening of HTTP request failed.",trace);}})
mod.SendFailed=Class("SendFailed",mod.Exception,function(publ,supr){publ.init=function(trace){supr(this).init("Sending of HTTP request failed.",trace);}})
var ASVRequest=Class("ASVRequest",function(publ){publ.init=function(){if((getURL==null)||(postURL==null)){throw"getURL and postURL are not available!";}else{this.readyState=0;this.responseText="";this.__contType="";this.status=200;}}
publ.open=function(type,url,async){if(async==false){throw"Can only open asynchronous connections!";}
this.__type=type;this.__url=url;this.readyState=0;}
publ.setRequestHeader=function(name,value){if(name=="Content-Type"){this.__contType=value;}}
publ.send=function(data){var self=this;var cbh=new Object();cbh.operationComplete=function(rsp){self.readyState=4;self.responseText=rsp.content;if(this.ignoreComplete==false){if(self.onreadystatechange){self.onreadystatechange();}}}
cbh.ignoreComplete=false;try{if(this.__type=="GET"){getURL(this.__url,cbh);}else if(this.__type=="POST"){postURL(this.__url,data,cbh,this.__contType);}}catch(e){cbh.ignoreComplete=true;throw e;}}})
var getHTTP=function(){var obj;try{obj=new XMLHttpRequest();}catch(e){try{obj=new ActiveXObject("Msxml2.XMLHTTP.4.0");}catch(e){try{obj=new ActiveXObject("Msxml2.XMLHTTP")}catch(e){try{obj=new ActiveXObject("microsoft.XMLHTTP");}catch(e){try{obj=new ASVRequest();}catch(e){throw new mod.NoHTTPRequestObject("Neither Mozilla, IE nor ASV found. Can't do HTTP request without them.");}}}}}
return obj;}
mod.sendRequest=function(type,url,user,pass,data,headers,callback){var async=false;if(arguments[arguments.length-1]instanceof Function){var async=true;callback=arguments[arguments.length-1];}
var headindex=arguments.length-((async||arguments[arguments.length-1]==null)?2:1);if(arguments[headindex]instanceof Array){headers=arguments[headindex];}else{headers=[];}
if(typeof user=="string"&&typeof pass=="string"){if(typeof data!="string"){data="";}}else if(typeof user=="string"){data=user;user=null;pass=null;}else{user=null;pass=null;}
var xmlhttp=getHTTP();try{if(user!=null){xmlhttp.open(type,url,async,user,pass);}else{xmlhttp.open(type,url,async);}}catch(e){throw new mod.RequestOpenFailed(e);}
for(var i=0;i<headers.length;i++){xmlhttp.setRequestHeader(headers[i][0],headers[i][1]);}
if(async){xmlhttp.onreadystatechange=function(){if(xmlhttp.readyState==4){callback(xmlhttp);xmlhttp=null;}else if(xmlhttp.readyState==2){try{var isNetscape=netscape;try{var s=xmlhttp.status;}catch(e){callback(xmlhttp);xmlhttp=null;}}catch(e){}}}}
try{xmlhttp.send(data);}catch(e){if(async){callback(xmlhttp,e);xmlhttp=null;}else{throw new mod.SendFailed(e);}}
return xmlhttp;}
mod.getURL=function(url,user,pass,headers,callback){var a=new Array("GET");for(var i=0;i<arguments.length;i++){a.push(arguments[i]);}
return mod.sendRequest.apply(this,a)}
mod.postURL=function(url,user,pass,data,headers,callback){var a=new Array("POST");for(var i=0;i<arguments.length;i++){a.push(arguments[i]);}
return mod.sendRequest.apply(this,a)}})
