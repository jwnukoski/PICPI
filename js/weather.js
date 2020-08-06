// AJAX Weather
// https://www.metaweather.com/api/
let weatherElement = document.getElementById('weather');

async function getWeatherAW(proxy, woeId) {
    try {
        const result = await fetch(`${proxy}https://www.metaweather.com/api/location/${woeId}`);
        const data = await result.json();

        weatherElement.innerHTML = `${data.parent.title} <br>
        High: ${Math.round(data.consolidated_weather[0].max_temp)}C <br>
        Low: ${Math.round(data.consolidated_weather[0].min_temp)}C`;

        return data;
    } catch (error) {
        console.log('Error in weather.js');
    }
}
let weatherData = getWeatherAW('https://cors-anywhere.herokuapp.com/', 2459115).then(result => console.log(result));


