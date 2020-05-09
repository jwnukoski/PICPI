// AJAX Weather
// https://www.metaweather.com/api/
fetch('https://cors-anywhere.herokuapp.com/https://www.metaweather.com/api/location/2459115/')
.then(result => {
    return result.json();
})
.then(data => {
    console.log(data);
})
.catch(error => {
    console.log(error);
});

