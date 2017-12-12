var radioInfected = false;
var fileLoaded = false;

function enableSubmit(){
    if(radioInfected && fileLoaded){
        console.log("enabling button")
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
    fail = validateUsername(form.username.value)
    fail += validatePassword(form.password.value)
    if(fail == ""){
        return true
    } 
    alert(fail)
    return false
}

function validateUsername(username){
    if(username == "") 
        return "No username was entered.\n"
    else if(username.length < 5)
        return "Username must be at least 5 characters long.\n"
    else if(/[^a-zA-Z0-9_-]/.test(username))
        return "Only english letters, digits, underscores, and dashs are valid input.\n"  
    return ""
}

function validatePassword(password){
    if(password == "") return "No password was entered.\n"
    else if(password.length < 5)
        return "Passwords must be at least 5 characters long!\n"
    else if(!/[a-z]/.test(password) || !/[A-Z]/.test(password) || !/[0-9]/.test(password))
        return "Passwords must contain at least:\n\t1 lowercase letter.\n\t1 uppercase letter.\n\t1 number."
    return ""
}
