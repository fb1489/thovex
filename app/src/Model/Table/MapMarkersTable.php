<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * MapMarkers Model
 *
 * @method \App\Model\Entity\MapMarker newEmptyEntity()
 * @method \App\Model\Entity\MapMarker newEntity(array $data, array $options = [])
 * @method array<\App\Model\Entity\MapMarker> newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\MapMarker get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \App\Model\Entity\MapMarker findOrCreate($search, ?callable $callback = null, array $options = [])
 * @method \App\Model\Entity\MapMarker patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method array<\App\Model\Entity\MapMarker> patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\MapMarker|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \App\Model\Entity\MapMarker saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method iterable<\App\Model\Entity\MapMarker>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MapMarker>|false saveMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MapMarker>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MapMarker> saveManyOrFail(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MapMarker>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MapMarker>|false deleteMany(iterable $entities, array $options = [])
 * @method iterable<\App\Model\Entity\MapMarker>|\Cake\Datasource\ResultSetInterface<\App\Model\Entity\MapMarker> deleteManyOrFail(iterable $entities, array $options = [])
 */
class MapMarkersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array<string, mixed> $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('map_markers');
        $this->setDisplayField('latLng');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('coordinates', 'create')
            ->notEmptyString('coordinates');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->allowEmptyString('title');

        return $validator;
    }
}
