// AJAX Weather
// https://www.metaweather.com/api/
async function getWeatherAW(proxy, woeId) {
    try {
        const result = await fetch(`${proxy}https://www.metaweather.com/api/location/${woeId}`);
        const data = await result.json();
        return data;
    } catch (error) {
        console.log('Error in weather.js');
    }
}
getWeatherAW('https://cors-anywhere.herokuapp.com/', 2459115).then(result => console.log(result));

