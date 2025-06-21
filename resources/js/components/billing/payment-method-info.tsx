import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { useTranslation } from "@/hooks/use-i18n"
import { CreditCard } from "lucide-react"
import type { FC } from "react"

interface PaymentMethod {
  id: string
  type: string
  card?: {
    brand: string
    last4: string
    exp_month: number
    exp_year: number
  }
}

interface PaymentMethodInfoProps {
  defaultPaymentMethod: PaymentMethod | null
  billingPortalUrl: string
}

export const PaymentMethodInfo: FC<PaymentMethodInfoProps> = ({ defaultPaymentMethod, billingPortalUrl }) => {
  const t = useTranslation()

  const formatCardBrand = (brand: string) => {
    switch (brand) {
      case "visa":
        return "Visa"
      case "mastercard":
        return "Mastercard"
      case "amex":
        return "American Express"
      case "discover":
        return "Discover"
      case "diners":
        return "Diners Club"
      case "jcb":
        return "JCB"
      case "unionpay":
        return "Union Pay"
      default:
        return brand.charAt(0).toUpperCase() + brand.slice(1)
    }
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{t("ui.billing.payment_method")}</CardTitle>
        <CardDescription>{t("ui.billing.manage_subscription_description")}</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        {defaultPaymentMethod?.card ? (
          <div className="flex items-center space-x-4">
            <div className="bg-muted flex h-12 w-12 items-center justify-center rounded-lg">
              <CreditCard className="h-6 w-6" />
            </div>
            <div className="flex-1">
              <p className="text-sm font-medium">
                {t("ui.billing.card_ending_in", {
                  brand: formatCardBrand(defaultPaymentMethod.card.brand),
                  last4: defaultPaymentMethod.card.last4,
                })}
              </p>
              <p className="text-muted-foreground text-sm">
                {t("ui.billing.expires", {
                  month: defaultPaymentMethod.card.exp_month.toString().padStart(2, "0"),
                  year: defaultPaymentMethod.card.exp_year,
                })}
              </p>
            </div>
          </div>
        ) : (
          <div className="text-muted-foreground flex items-center justify-center py-8">
            <div className="text-center">
              <CreditCard className="mx-auto mb-4 h-12 w-12" />
              <p className="text-sm">{t("ui.billing.no_payment_method")}</p>
            </div>
          </div>
        )}

        <div className="pt-4">
          <Button asChild>
            <a href={billingPortalUrl} target="_blank" rel="noopener noreferrer">
              {t("ui.billing.open_billing_portal")}
            </a>
          </Button>
        </div>
      </CardContent>
    </Card>
  )
}
