terraform {
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "~> 3.0"
    }
  }
}

provider "azurerm" {
  features {}
  subscription_id = "f55dab3f-5650-496e-9ebf-e9276c01df96"
  tenant_id       = "50c76291-0c80-4444-a2fb-4f8ab168c311"
}

resource "azurerm_resource_group" "ct_viewer" {
  name     = "ct-viewer-rg"
  location = "Switzerland North"

  lifecycle {
    ignore_changes = [location]
  }
}

resource "azurerm_service_plan" "ct_viewer" {
  name                = "ct-viewer-plan"
  resource_group_name = azurerm_resource_group.ct_viewer.name
  location            = "Switzerland North"
  os_type             = "Linux"
  sku_name            = "F1"

  lifecycle {
    ignore_changes = [location]
  }
}

resource "azurerm_linux_web_app" "ct_viewer" {
  name                = "ct-bone-viewer"
  resource_group_name = azurerm_resource_group.ct_viewer.name
  location            = "Switzerland North"
  service_plan_id     = azurerm_service_plan.ct_viewer.id
  https_only          = true

  lifecycle {
    ignore_changes = [location, site_config, app_settings, 
                      client_affinity_enabled,
                      ftp_publish_basic_authentication_enabled,
                      webdeploy_publish_basic_authentication_enabled]
  }

  site_config {
    application_stack {
      php_version = "8.2"
    }
  }
}

resource "azurerm_service_plan" "ct_api" {
  name                = "ct-api-plan"
  resource_group_name = azurerm_resource_group.ct_viewer.name
  location            = "Switzerland North"
  os_type             = "Linux"
  sku_name            = "F1"
}

resource "azurerm_linux_web_app" "ct_api" {
  name                = "ct-bone-api"
  resource_group_name = azurerm_resource_group.ct_viewer.name
  location            = "Switzerland North"
  service_plan_id     = azurerm_service_plan.ct_api.id
  https_only          = true

  site_config {
    always_on = false
    application_stack {
      php_version = "8.2"
    }
  }
}