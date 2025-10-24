document.addEventListener("DOMContentLoaded", () => {
    const content = document.querySelector(".content");

    // Add a subtle interactive light effect
    content.addEventListener("mousemove", (e) => {
        const { left, top, width, height } = content.getBoundingClientRect();
        const x = (e.clientX - left) / width;
        const y = (e.clientY - top) / height;
        content.style.boxShadow = `${(x - 0.5) * 20}px ${(y - 0.5) * 20}px 50px rgba(0,123,255,0.15)`;
    });

    content.addEventListener("mouseleave", () => {
        content.style.boxShadow = "0 8px 25px rgba(0, 0, 0, 0.1)";
    });
});
