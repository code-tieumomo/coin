<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // Subnet Permissions
    case SUBNET_CREATE = 'subnet|create';
    case SUBNET_READ = 'subnet|read';
    case SUBNET_UPDATE = 'subnet|update';
    case SUBNET_DELETE = 'subnet|delete';
    case SUBNET_RESTORE = 'subnet|restore';
    case SUBNET_FORCE_DELETE = 'subnet|force_delete';
}
