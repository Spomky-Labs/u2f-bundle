<?php

namespace U2FAuthentication\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use U2FAuthentication\Bundle\DependencyInjection\U2FAuthenticationExtension;

class U2FAuthenticationBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new U2FAuthenticationExtension();
    }

}
