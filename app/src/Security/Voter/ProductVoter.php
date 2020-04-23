<?php

namespace App\Security\Voter;

use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['PRODUCT_EDIT', 'PRODUCT_VIEW'])
            && $subject instanceof \App\Entity\Product;
    }

    /**
     * @param string $attribute
     * @param Product $subject
     * @param TokenInterface $token
     * @return bool
     * @author Morteza Karimi <me@morteza-karimi.ir>
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'PRODUCT_EDIT':
                // logic to determine if the user can EDIT
                // return true or false
                break;
            case 'PRODUCT_VIEW':
                // logic to determine if the user can VIEW
                // return true or false
                break;
        }

        return false;
    }
}
