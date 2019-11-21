window.onscroll = function(){
    var imgs = document.querySelectorAll('img[data-src]');
    
    for (var i=0; i < imgs.length; i++){
        if(imgs[i].getBoundingClientRect().top < window.innerHeight){
            imgs[i].src = imgs[i].getAttribute('data-src');
            console.log("Lazy-load");
        }   
    }
};