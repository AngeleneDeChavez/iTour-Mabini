// Initialize or get the page view count from local storage
let pageViews = localStorage.getItem('pageViews') ? parseInt(localStorage.getItem('pageViews')) : 0;
    
// Increment the page view count
pageViews++;

// Save the updated count to local storage
localStorage.setItem('pageViews', pageViews);

// Display the updated count
document.getElementById('pageViews').innerText = pageViews;