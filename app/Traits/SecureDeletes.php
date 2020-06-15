<?php

namespace App\Traits;

use Exception;

trait SecureDeletes
{
    /**
     * Force delete only when there is no reference to other models 
     * otherwise it will be soft delete
     * 
     * @throws Exception
     * 
     * @return bool
     */
    public function secureDelete()
    {
        try {
            (clone $this)->forceDelete();
        } catch (Exception $exception) {
            // Check for Integrity constraint violation
            // and Check if a foreign key constraint fails
            if ($exception->getCode() == 23000 && $exception->errorInfo[1] == 1451 || ($exception->getCode() == 23503 && $exception->errorInfo[1] == 7) ) {
                $this->delete();
            } else {
                throw $exception;
            }
        }
    }
}