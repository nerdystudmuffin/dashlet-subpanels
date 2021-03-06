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
Module("crypto","0.1.2",function(mod){mod.listEncrypters=function(){var c=[];for(var attr in String.prototype){if(attr.slice(0,8)=="encrypt_"){c.push(attr.slice(8));}}
return c;}
mod.listDecrypters=function(){var c=[];for(var attr in String.prototype){if(attr.slice(0,8)=="decrypt_"){c.push(attr.slice(8));}}
return c;}
String.prototype.encrypt=function(crydec){var n="encrypt_"+crydec;if(String.prototype[n]){var args=[];for(var i=1;i<arguments.length;i++){args[i-1]=arguments[i];}
return String.prototype[n].apply(this,args);}else{throw new mod.Exception("Decrypter '%s' not found.".format(crydec));}}
String.prototype.decrypt=function(crydec){var n="decrypt_"+crydec;if(String.prototype[n]){var args=[];for(var i=1;i<arguments.length;i++){args[i-1]=arguments[i];}
return String.prototype[n].apply(this,args);}else{throw new mod.Exception("Encrypter '%s' not found.".format(crydec));}}
String.prototype.encrypt_xor=function(key){var e=new Array(this.length);var l=key.length;for(var i=0;i<this.length;i++){e[i]=String.fromCharCode(this.charCodeAt(i)^key.charCodeAt(i%l));}
return e.join("");}
String.prototype.decrypt_xor=String.prototype.encrypt_xor;String.prototype.encrypt_rc4=function(key){var sbox=new Array(256);for(var i=0;i<256;i++){sbox[i]=i;}
var j=0;for(var i=0;i<256;i++){j=(j+sbox[i]+key.charCodeAt(i%key.length))%256;var tmp=sbox[i];sbox[i]=sbox[j];sbox[j]=tmp;}
var i=256;var j=256;var rslt=new Array(this.length);for(var k=0;k<this.length;k++){i=(i+1)%256;j=(j+sbox[i])%256;var tmp=sbox[i];sbox[i]=sbox[j];sbox[j]=tmp;t=(sbox[i]+sbox[j])%256;rslt[k]=String.fromCharCode(this.charCodeAt(k)^sbox[t]);}
return rslt.join("");}
String.prototype.decrypt_rc4=String.prototype.encrypt_rc4;})
