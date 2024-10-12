function accept_tos() {
    document.querySelector(".tos-box").classList.add("tos-slide-out")
    setTimeout(() => { document.querySelector(".tos-box").remove()  }, 1500);
}