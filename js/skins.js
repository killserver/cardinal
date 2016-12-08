var div = document.createElement("link");
div.rel = "stylesheet";
div.href = default_link+cssRebuildLink+"?windowWidth="+window.innerWidth+"&windowHeight="+window.innerHeight+"&documentWidth="+window.screen.width+"&documentHeight="+window.screen.height;
document.getElementById("skinRebuilded").removeChild(document.getElementById("removedSkinRebuilded"));
document.getElementById("skinRebuilded").appendChild(div);