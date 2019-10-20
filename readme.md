<p align="center"><img src="https://gatortest.tk/hgccimages/snappy.png" width="250"></p>
<h1 align="center">HostGator CodeChallenge</h1>

## The Challenge

**Create a PHP Web API that consults the Cat API: https://docs.thecatapi.com**</br>
The API must allow the search for Cat Breeds by name. The API should cache the results from the
Cat API into a local MySQL database. If the cat breed is not found in the local MySQL database, it
will query the cat API. The only endpoint that must be implemented is the GET to /breeds

## The Solution

The challenge was solved using Laravel Framework for the base architecture. The only endpoints the API implements are GET to /breeds and GET to /breeds/{id}

Both endpoints share the same controller (<code>ContentController</code>), wich has two methods for handling the requests: <code>getContentByName</code> and <code>getContentById</code>

The <code>WebApiController</code> was created in order to implement a custom trait: the <code>WebApiResponser</code>. The <code>WebApiResponser</code> trait contains all the methods used on the <code>ContentController</code> (wich extends from the <code>WebApiController</code>) in order to keep the code clean and modularized.

When the api recives a request will search the data into a MySQL local database, if the data is not found, then it will query The Cat API, store the data on the database for 30 days and finally will be retrieved to the client. Requests to The Cat API are made through a Guzzle HTTP Client.

In order to increase the security of the api a middleware was implemented in both endpoints to limit the number of requests to 15 requests per minute per IP. This will help blocking malicious bots and it can mitigate DOS attacks.

Almost every possible exception is being handled by the <code>App\Exceptions\Handler.php</code>. The other few possible errors are being handled from both <code>ContentController</code> and <code>WebApiResponser</code>. Errors will always return in JSON format.

<h1 align="center">Search by Name Flow Diagram</h1>
<p align="center"><img src="https://gatortest.tk/hgccimages/Flow-Diagram-BreedsByName.png" width="700"></p>
<br>
<h1 align="center">Search by ID Flow Diagram</h1>
<p align="center"><img src="https://gatortest.tk/hgccimages/Flow-Diagram-BreedById.png" width="700"></p>






<br>
<h1 align="center">Documentation</h1>

## Search by Breed Name
- Breeds search by name can be requested via <span style="color:green">GET</span> <a>http://localhost:XX/breeds</a>
- The only query parameter accepted is <code><i>name</i></code>

Example:<br>
<a>http://localhost:XX/breeds?name=fo</a> <i>(Search all breed names containing "fo")</i>

## Search by Breed ID
- Breeds search by ID can be requested via <span style="color:green">GET</span> <a>http://localhost:XX/breeds/{id}</a>
- The <code>breed.id</code> is a unique 4 character id. The complete list of breeds with their ids can be accesed from https://api.thecatapi.com/v1/breeds

Example:<br>
<a>http://localhost:XX/breeds/beng</a> <i>(Search for the Bengal cats)</i>

<h1 align="center">Test Online</h1>

**You can try the api using the following link:**
<br>
https://api.gatortest.tk






