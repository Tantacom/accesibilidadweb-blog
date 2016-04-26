// JavaScript Document



var formsValidations = {
		setMsgError:function(txt, form){
		
			var parentForm = form.parent();
			var msgError = parentForm.find(".msgError");
			var divElement = (msgError.length != 0) ? msgError.eq(0) : document.createElement("div");		
			var ulElement = document.createElement("ul");
			var liElement = null;		
			var errors = txt.split("|");
			var msgConfirm = jQuery(".msgConfirm");			
			jQuery(divElement).attr("class", "msgError");
			jQuery(divElement).attr("tabIndex","-1");				
			
			if(jQuery(divElement).find("ul").length != 0) jQuery(divElement).empty();
			
			for(var i = 0; i < errors.length - 1; i++){
				liElement = document.createElement("li");
				liElement.appendChild(document.createTextNode(errors[i]));
				ulElement.appendChild(liElement);
			}
			
			jQuery(divElement).append(jQuery("<span>La publicación del comentario no ha podido realizarse por estos motivos:</span>"));
			jQuery(divElement).append(ulElement);		
			if(msgError.length == 0) form.before(jQuery(divElement));			
			jQuery(divElement).focus();		
		
	},
	validaComentarios:function(obj){
		var errorTxt = "";
		var f = jQuery(obj);			
		var author = f.find("input#author");
		var email = f.find("input#email");
		var comentario = f.find("textarea#comment");
		var acepto = f.find("input#acepto");
		var condiciones = f.find("input:checkbox");
		var parentt;
		if(author.length != 0){
			parentt = author.parent();
			if(!author.val()){
				errorTxt += "El campo 'Nombre' es obligatorio |";
				parentt.addClass("error");
			}else parentt.removeClass("error");
			
			parentt = email.parent();
			if(!email.val()){
				errorTxt += "El campo 'Email' es obligatorio |";			
				parentt.addClass("error");
			}else{
				parentt.removeClass("error");
				if(!regularExpressions.isValidEmail(email.val())){
					errorTxt += "El formato del campo 'Email' no es correcto |";			
					parentt.addClass("error");
				}else parentt.removeClass("error");
			}
		}
				
		parentt = comentario.parent();
		
		if(!comentario.val()){
			errorTxt += "El campo 'Comentario' es obligatorio |";
			parentt.addClass("error");
		}else parentt.removeClass("error");
		
		
		parentt = condiciones.parent();
		
		if(!condiciones.is(":checked")){
			errorTxt += "Debes aceptar las condiciones legales |";
			parentt.addClass("error");
		}else parentt.removeClass("error");
		
				
		
		if(errorTxt != ""){				
			formsValidations.setMsgError(errorTxt, f);
			return false;
		}else{			
			return true;
		}
	}
		
}

/* expresiones regulares para validar formularios */
var regularExpressions = {	
	isValidEmail:function (str){
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
		return (filter.test(str));
	}
}


jQuery(function () {	
	
	
	if(jQuery("#commentform").length != 0){
		jQuery("#commentform").submit(function(){return formsValidations.validaComentarios( jQuery(this)) })	
	}
	
	
});
