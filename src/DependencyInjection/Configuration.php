<?php

namespace SLLH\HybridAuthBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Description of Configuration
 *
 * @author Sullivan SENECHAL <soullivaneuh@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder $builder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $rootNode = $builder->root('sllh_hybrid_auth');

        $rootNode
        // FIXME : xml adptable config
//            ->fixXmlConfig('hybridauth_config')
            ->children()
                ->scalarNode('firewall_name')->defaultValue(false)->end()
                ->arrayNode('connect')
                    ->children()
                        ->booleanNode('auth_after_register')->defaultValue(true)->end()
                        ->arrayNode('services')
                            ->children()
                                ->scalarNode('connect_provider')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('registration_form_handler')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                                ->scalarNode('registration_form')
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('hybridauth_config')
                    ->isRequired(true)
                    ->children()
                        ->scalarNode('base_url')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('debug_file')
                            ->defaultValue("")
                        ->end()
                        ->arrayNode('providers') // TODO: add params for authenticate => http://hybridauth.sourceforge.net/apidoc.html
                            ->isRequired(true)
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('enabled')
                                        ->defaultValue(true)
                                    ->end()
                                    ->arrayNode('scope')
                                        ->prototype('scalar')
                                        ->end()
                                    ->end()
                                    ->arrayNode('keys')
                                        ->isRequired(true)
                                        ->children()
                                            ->scalarNode('id')
                                            ->end()
                                            ->scalarNode('key')
                                            ->end()
                                            ->scalarNode('secret')
                                                ->cannotBeEmpty()
                                            ->end()
                                        ->end()
                                        ->validate()
                                            ->ifTrue(function($k) {
                                                return !isset($k['id']) && !isset($k['key']);
                                            })
                                            ->thenInvalid('Bad keys %s')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}

?>
