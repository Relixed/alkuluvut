<?php
	// Setup slim library
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	require 'vendor/autoload.php';

	$app = new \Slim\App;

	function is_prime($n)
	{
		if(!is_numeric($n)) { return false; } // Or throw error?

		// Perform check for the parametre number
		for ($i = 2; $i < $n; $i++) {
			if ($n % $i == 0) {
				return false;
			}               
		}
		
		return true;
	}

	$app->get('/', function(Request $request, Response $response, array $args)
	{
		// Get external file contents, assume that the file always exist within the demo folder
		return $response->write(file_get_contents("index.html")); 
	});

	$app->get('/primeapi', function (Request $request, Response $response, array $args)
	{
		// Get request for checking wheter a number is prime or not
		// Accepts either a single number or a comma separed string of numbers

		$params = $request->getQueryParams();
		$errResponse = json_encode(['error' => 'Invalid query']); // Generic error response

		// Chekc that all of the required parametres actually exists in the request
		if(isset($params['action'])) {
			// Check what we're doing, a single or sum test
			switch($params['action']) {
				case 'checkprime':
					// Testing a single number
					if(isset($params['number'])) {
						$response->getBody()->write(json_encode([
							'isPrime' => is_prime($params['number'])
						]));
					} else {
						$response->getBody()->write($errResponse);
					}

					break;
				case 'sumandcheck':
					// Testing sum of array of numbers
					if(isset($params['numbers'])) {
						// Make array out of user input, remove possible spacechar characters
						$numbers = explode(',', str_replace(' ', '', $params['numbers']));

						$response->getBody()->write(json_encode([
							'isPrime' => is_prime(array_sum($numbers)) // Sum the array
						]));
					} else {
						$response->getBody()->write($errResponse);
					}

					break;
				default:
					$response->getBody()->write($errResponse);
			}
		} else {
			$response->getBody()->write($errResponse);
		}

		return $response;
	});

	$app->run();
