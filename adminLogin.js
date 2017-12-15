var radioInfected = false;
var fileLoaded = false;

function enableSubmit(){
    if(radioInfected && fileLoaded){
        document.getElementById("sb").disabled=false
    } else {
        document.getElementById("sb").disabled=true
    }
}

function radioInput(){
    radioInfected = true
    enableSubmit()
}

function fileInput(file){
    if(file.files.length == 0){
        fileLoaded = false
    } else {
        fileLoaded = true
    }
    enableSubmit()
}

function validate(form){
    console.log("validate")
    console.log(form.username.value)
    console.log(form.password.value)
    return false
}
