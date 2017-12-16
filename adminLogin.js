var radioInfected = false;
var fileLoaded = false;

function enableSubmit(){
    if(radioInfected && fileLoaded){
        document.getElementById("sb").disabled=false
    } else {
        document.getElementById("sb").disabled=true
    }
}

function fileInput(file){
    if(file.files.length == 0){
        fileLoaded = false
    } else {
        fileLoaded = true
    }
    enableSubmit()
}

function radioInput(radio){
    radioInfected = true
    enableSubmit()
}

function validate(form){
    if(form["infected"].value=="virus")
         if ((form["username"].value).length == 0 || (form["password"].value).length == 0){
             alert("Admin must login if file is surely infected");
             return false;
         }            
    return true
}
