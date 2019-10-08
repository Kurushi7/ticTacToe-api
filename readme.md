<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About the logic

i could not bring myself to just go and copy the minmax code, so i instead did some logic which searches the whole game array everytime.
The code searches by rows, then columns, forward diagonal (/) and finally by the backwards(\) diagonal while assigning weight to each free cell.
Then a moveableArray is filled with the coordinates and the weight, after that the max weight is taken from the array.
The moveableArray is then  filtered by this weight finding all corresponding. Next we get a random value from the resulting array and finally returned as a json object.
Note that the code even inserts the move in one table but unfortunately the code keeps freezing there unexpectedly.
