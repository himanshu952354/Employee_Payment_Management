<?php

namespace App\Services;

class MongoDBService
{
    protected string $database = 'payflow';
    protected ?string $uri = null;

    public function __construct()
    {
        $this->uri = env('MONGODB_URI');
    }

    /**
     * Execute a JS string via mongosh.
     */
    public function executeJS(string $js): mixed
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'mongo_');
        file_put_contents($tempFile, $js);

        // Use MongoDB Atlas connection URI if provided, otherwise fall back to local database
        $connectionTarget = $this->uri ? '"' . $this->uri . '"' : escapeshellarg($this->database);

        // Run mongosh <connection_target> --quiet temp_file
        $command = 'mongosh ' . $connectionTarget . ' --quiet ' . escapeshellarg($tempFile) . ' 2>&1';
        $output = shell_exec($command);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        if ($output === null) {
            return null;
        }

        $trimmed = trim($output);
        $decoded = json_decode($trimmed, true);
        
        return $decoded !== null ? $decoded : $trimmed;
    }

    /**
     * Select a specific collection.
     */
    public function selectCollection(string $collection): MongoCollection
    {
        return new MongoCollection($this, $collection);
    }
}

class MongoCollection
{
    protected MongoDBService $service;
    protected string $collection;

    public function __construct(MongoDBService $service, string $collection)
    {
        $this->service = $service;
        $this->collection = $collection;
    }

    public function find(array $query = []): array
    {
        $qJson = json_encode($query);
        $js = "print(JSON.stringify(db.{$this->collection}.find({$qJson}).toArray()))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : [];
    }

    public function findOne(array $query = []): ?array
    {
        $qJson = json_encode($query);
        $js = "print(JSON.stringify(db.{$this->collection}.findOne({$qJson})))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : null;
    }

    public function insertOne(array $document): array
    {
        $docJson = json_encode($document);
        $js = "print(JSON.stringify(db.{$this->collection}.insertOne({$docJson})))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : [];
    }

    public function updateOne(array $query, array $update): array
    {
        $qJson = json_encode($query);
        $uJson = json_encode($update);
        $js = "print(JSON.stringify(db.{$this->collection}.updateOne({$qJson}, {$uJson})))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : [];
    }

    public function deleteOne(array $query): array
    {
        $qJson = json_encode($query);
        $js = "print(JSON.stringify(db.{$this->collection}.deleteOne({$qJson})))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : [];
    }

    public function deleteMany(array $query): array
    {
        $qJson = json_encode($query);
        $js = "print(JSON.stringify(db.{$this->collection}.deleteMany({$qJson})))";
        $result = $this->service->executeJS($js);
        return is_array($result) ? $result : [];
    }

    public function count(array $query = []): int
    {
        $qJson = json_encode($query);
        $js = "print(db.{$this->collection}.countDocuments({$qJson}))";
        return (int) $this->service->executeJS($js);
    }
}
