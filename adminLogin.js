var radioInfected = false;
var fileLoaded = false;

/*
 * Enables submit button if both file is uploaded and radio button is selected
 */
function enableSubmit(){
    if(radioInfected && fileLoaded){
        document.getElementById("sb").disabled=false
    } else {
        document.getElementById("sb").disabled=true
    }
}

/**
 * Checks if a file is loaded, then attempts to enable submit button
 * @param {*} file text file 
 */
function fileInput(file){
    if(file.files.length == 0){
        fileLoaded = false
    } else {
        fileLoaded = true
    }
    enableSubmit()
}

/**
 * if possibly a virus is selected, disable non required fields, UN/PW/Virus name
 * if surely a virus is selected, enable required fields, UN/PW/virus name
 * @param {*} radio 
 */
function radioInput(radio){
    radioInfected = true

    if(radio.value == "virus"){
        document.getElementById("inputForm")["username"].disabled=false
        document.getElementById("inputForm")["password"].disabled=false
        document.getElementById("inputForm")["vname"].disabled=false
    } else{
        document.getElementById("inputForm")["username"].disabled=true
        document.getElementById("inputForm")["password"].disabled=true
        document.getElementById("inputForm")["vname"].disabled=true
    }
    enableSubmit()
}

/**
 * Checks if loaded file is surely, or possibly a virus.
 * If surely, verify username and password exist.
 * @param {*} form 
 */
function validate(form){
    if(form["infected"].value=="virus")
         if ((form["username"].value).length == 0 || (form["password"].value).length == 0){
             alert("Admin must login if file is surely infected");
             return false;
         } else {
            return validateMalwareName(form["vname"].value)
         }
    return true;
}

/**
 * checks if a virus name is entered before submission.
 * @param {*} virusname 
 */
function validateMalwareName(virusname){
    if(virusname == "") {
        alert("No virus name was entered.\n")
        return false
    }
    else if(/[^a-zA-Z0-9]/.test(virusname)){
        alert("Virus name can only contain:\n\t- English Letters (upper and lower case)\n\t- Numbers")
        return false
    }
    return true
}
