<?php

namespace Example\BlogApp\Test\Api\Generated;

use Example\BlogApp\TableGateways;
use Example\BlogApp\Models\UsersModel;
use Example\BlogApp\Services\UsersService;

class UsersEndpointTest extends \⌬\Tests\RoutesTestCase
{

    const MODEL_NAME = 'Users';

    public function testUsersCreate()
    {
        $this->waypoint("Begin");
        /** @var UsersService $service */
        $service = $this->getApp()->getContainer()->get(UsersService::class);
        /** @var UsersModel $newUsers **/
        $newUsers = $service->getMockObject();

        $this->waypoint("Initialise Mock Model");
        $newUsersArray = $newUsers->__toArray();

        $method = "PUT";
        $uri    = "/v1/users";

        $response = $this->request(
            $method,
            $uri,
            $newUsersArray
        );
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();

        ob_start();
        \Kint::dump(
            $newUsersArray,
            json_encode($newUsersArray, JSON_PRETTY_PRINT)
        );
        $requestUsersParams = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(
            in_array(
                $response->getStatusCode(),
                [
                    200,
                    400,
                ]
            ),
            "Response was not expected 200 or 400.\n" .
            "Request: {$method} => {$uri}" .
            "{$requestUsersParams}\n" .
            "Response body is: \n" .
            " ******************************\n{$body}\n ******************************\n"
        );

        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newUsers->__toArray()) . "\""
        );
        $responseJson = json_decode($body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to PUT /v1/users returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Users', $responseJson);
        $this->waypoint("Some assertions");

        #\Kint::dump(
        #    $newUsersArray,
        #    $responseJson
        #);

        $this->validateUsersObject($responseJson['Users']);
        $this->waypoint("Validate Object Response");
        //TODO: Make this respect primary key field instead of assuming ID.
        if(!isset($responseJson['Users']['Id'])){
            $this->markTestIncomplete("Skipped test... Users response object doesn't have an ID field.");
        }

        return $responseJson['Users']['Id'];
    }


    public function testUsersCreateFails()
    {
        $this->waypoint("Begin");

        $newUsers = [
            'id' => null,
            'displayName' => null,
            'userName' => null,
            'email' => null,
            'password' => null,
        ];
        $response = $this->request("PUT", "/v1/users", $newUsers);
        $this->waypoint("API PUT REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\" Request JSON blob: \"" . json_encode($newUsers) . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Fail", $responseJson['Status'], "Object was created, when failure was expected.");
        $this->waypoint("Some assertions");
    }

    /**
     * @depends testUsersCreate
     */
    public function testUsersGet($id)
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/users/{$id}");
        $this->waypoint("API GET REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/users/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Users', $responseJson);
        $this->waypoint("Some assertions");

        $this->validateUsersObject($responseJson['Users']);
        $this->waypoint("Validate Object Response");
    }

    /**
     * @depends testUsersCreate
     */
    public function testUsersList()
    {
        $this->waypoint("Begin");
        $response = $this->request("GET", "/v1/users");
        $this->waypoint("API REST REQUEST");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->waypoint("Get & Parse Response");
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->waypoint("JSON DECODE");
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to GET /v1/users returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertArrayHasKey('Users', $responseJson);
        $this->waypoint("Some assertions");
        $this->validateUsersObject(reset($responseJson['Users']));
        $this->waypoint("Validate Object Response");

    }

    /**
     * @depends testUsersCreate
     */
    public function testUsersDelete($id)
    {
        $response = $this->request("DELETE", "/v1/users/{$id}");
        $body = (string) $response->getBody();
        $this->assertTrue(in_array($response->getStatusCode(), [200,400]), "Response was not expected 200 or 400. Response body is: \n\n" . $body . "\n\n");        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertArrayHasKey('Status', $responseJson);
        $this->assertEquals("Okay", $responseJson['Status'], "Verify that request to DELETE /v1/users/{$id} returns an \"Status: Okay\" response.  This failed. " . (isset($responseJson['Reason']) ? "Reason: " . $responseJson['Reason'] : "No Reason Given"));
        $this->assertEquals("DELETE", $responseJson['Action']);
        $this->assertArrayHasKey('Users', $responseJson);
        $this->validateUsersObject($responseJson['Users']);
        return $id;
    }

    /**
     * @depends testUsersDelete
     */
    public function testUsersDeleteVerify($id)
    {
        $response = $this->request("GET", "/v1/users/{$id}");
        $body = (string) $response->getBody();
        $this->assertNotNull(
            json_decode((string) $body),
            "Assert that the JSON response is actually JSON that is parsable failed. Response was: \"". (string) $body . "\""
        );
        $responseJson = json_decode((string)$body, true);
        $this->assertEquals("Fail", $responseJson['Status']);
    }

    private function validateUsersObject($UsersObject)
    {
        $this->assertArrayHasKey('Id', $UsersObject, "There was no element with the key 'id'.");
        $this->assertArrayHasKey('DisplayName', $UsersObject, "There was no element with the key 'displayName'.");
        $this->assertArrayHasKey('UserName', $UsersObject, "There was no element with the key 'userName'.");
        $this->assertArrayHasKey('Email', $UsersObject, "There was no element with the key 'email'.");
        $this->assertArrayHasKey('Password', $UsersObject, "There was no element with the key 'password'.");
    }
}
