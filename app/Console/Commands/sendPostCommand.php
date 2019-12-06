<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Goutte\Client;

class sendPostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'customCommand
            { url : https://atomic.incfile.com/fakepost} {statusCode=200 : status code expected }';

    protected $collections = [
        'animation',
        'ufc',
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the endpoint is active ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            $url = $this->getUrl();
            $expectedCode = 200;
            $crawler = $this->client->request('GET', $url);
            $statusCode  = $this->client->getResponse()->getStatusCode();

        } catch (\Exception $e) {

            $this->error(" Exception: Failed link for $url");
            $this->error($e->getMessage());

            return 2;
        }

        if ($statusCode !== $expectedCode) {

            $this->error("Failed  link for $url with a status: '$statusCode' (expected code: '$expectedCode')");

            return 1;
        }

        $this->info("Success link URL $url!");

        $this->display($this->getInformation());

        return 0;
    }

    /**
     * Example response information.
     * @return array
     */

    private function getInformation()
    {
        //Crear simulación de respuesta de la petición a la página

        $statusCode= ['200','400'];

        for($i = 0; $i<200;$i++){
            $response[] =
             [
                'status' => (array_rand(array_flip($statusCode))),
                'description' =>'lorem',
                'number' =>rand(1,500),
            ];
        }
        return $response;

    }

    private function getUrl()
    {
        $url = $this->argument('url');

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception("Invalid URL '$url'");
        }

        return $url;
    }

    private function display($response)
    {

        $headers = ['post','content','message'];

        $this->table($headers,$this->isValid($response));
    }

    /**
     * Check if array is vailid
     *
     * @param array $information
     *
     * @return array
     */

    private function isValid(array $infomation)
    {
        $correct = array();
        $error  = array();

        /*Primero se tendría que validar si todas las solicitudes tienen la misma estructura
          para el ejemplo daremos por hecho que si
        */

        //Ahora se valida si cumple con la validación establecida y se trata la respuesta como la necesito ver

        foreach ($infomation as $value) {

            if ($value['status'] == '200') {
                $correct[] =
                [
                    'post'    => $value['number'],
                    'content' => $value['description'].'. Request: Success',
                    'message' => 'Correct',
                ];
            }
            else{
                $error[] =
                [
                    'post'    => $value['status'],
                    'content' => 'Request: Incorrect',
                    'message' => 'Incorrect information request',
                ];
            }
        }

        return $this->isRequiredLength($correct)? $correct : $error;

    }

    private function isRequiredLength(array $result):bool
    {

        return (count($result) > 0 ? true : false);
    }

}
