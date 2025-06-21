import { Badge } from "@/components/ui/badge"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { useTranslation } from "@/hooks/use-i18n"
import { format, parseISO } from "date-fns"
import type { FC } from "react"

interface Subscription {
  name: string
  stripe_status: string
  stripe_price: string
  quantity: number
  trial_ends_at?: string
  ends_at?: string
  created_at: string
  on_trial: boolean
  canceled: boolean
  on_grace_period: boolean
  recurring: boolean
}

interface SubscriptionInfoProps {
  subscription: Subscription | null
}

const getStatusBadgeVariant = (status: string, onTrial: boolean, canceled: boolean, onGracePeriod: boolean) => {
  if (onTrial) return "secondary"
  if (canceled || onGracePeriod) return "destructive"
  if (status === "active") return "default"
  if (status === "past_due" || status === "unpaid") return "destructive"
  return "secondary"
}

const getStatusText = (subscription: Subscription, t: (key: string) => string) => {
  if (subscription.on_trial) return t("ui.billing.status_trialing")
  if (subscription.canceled) return t("ui.billing.status_canceled")
  if (subscription.on_grace_period) return t("ui.billing.on_grace_period")

  switch (subscription.stripe_status) {
    case "active":
      return t("ui.billing.status_active")
    case "canceled":
      return t("ui.billing.status_canceled")
    case "incomplete":
      return t("ui.billing.status_incomplete")
    case "incomplete_expired":
      return t("ui.billing.status_incomplete_expired")
    case "past_due":
      return t("ui.billing.status_past_due")
    case "unpaid":
      return t("ui.billing.status_unpaid")
    default:
      return subscription.stripe_status
  }
}

export const SubscriptionInfo: FC<SubscriptionInfoProps> = ({ subscription }) => {
  const t = useTranslation()

  if (!subscription) {
    return (
      <Card>
        <CardHeader>
          <CardTitle>{t("ui.billing.current_plan")}</CardTitle>
          <CardDescription>{t("ui.billing.no_subscription_description")}</CardDescription>
        </CardHeader>
        <CardContent>
          <div className="text-muted-foreground flex items-center justify-center py-12">
            <div className="text-center">
              <h3 className="text-lg font-medium">{t("ui.billing.no_subscription")}</h3>
              <p className="mt-2 text-sm">{t("ui.billing.no_subscription_description")}</p>
            </div>
          </div>
        </CardContent>
      </Card>
    )
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{t("ui.billing.current_plan")}</CardTitle>
        <CardDescription>{t("ui.billing.description")}</CardDescription>
      </CardHeader>
      <CardContent className="space-y-4">
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
          <div>
            <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.plan_name")}</dt>
            <dd className="mt-1 text-sm">{subscription.name}</dd>
          </div>

          <div>
            <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.status")}</dt>
            <dd className="mt-1">
              <Badge
                variant={getStatusBadgeVariant(
                  subscription.stripe_status,
                  subscription.on_trial,
                  subscription.canceled,
                  subscription.on_grace_period,
                )}
              >
                {getStatusText(subscription, t)}
              </Badge>
            </dd>
          </div>

          {subscription.quantity > 1 && (
            <div>
              <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.quantity")}</dt>
              <dd className="mt-1 text-sm">{subscription.quantity}</dd>
            </div>
          )}

          <div>
            <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.created_date")}</dt>
            <dd className="mt-1 text-sm">{format(parseISO(subscription.created_at), "MMM d, yyyy")}</dd>
          </div>

          {subscription.trial_ends_at && (
            <div>
              <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.trial_ends")}</dt>
              <dd className="mt-1 text-sm">{format(parseISO(subscription.trial_ends_at), "MMM d, yyyy")}</dd>
            </div>
          )}

          {subscription.ends_at && (
            <div>
              <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.subscription_ends")}</dt>
              <dd className="mt-1 text-sm">{format(parseISO(subscription.ends_at), "MMM d, yyyy")}</dd>
            </div>
          )}

          {subscription.recurring && !subscription.trial_ends_at && !subscription.ends_at && (
            <div>
              <dt className="text-muted-foreground text-sm font-medium">{t("ui.billing.next_billing_date")}</dt>
              <dd className="mt-1 text-sm">
                {/* Note: We would need the actual next billing date from Stripe */}
                {t("ui.billing.contact_support_billing")}
              </dd>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  )
}
