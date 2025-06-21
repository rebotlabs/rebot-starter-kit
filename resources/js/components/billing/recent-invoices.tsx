import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table"
import { useTranslation } from "@/hooks/use-i18n"
import { format, parseISO } from "date-fns"
import { Download, ExternalLink, Receipt } from "lucide-react"
import type { FC } from "react"

interface Invoice {
  id: string
  date: string
  total: string
  hosted_invoice_url: string
  invoice_pdf: string
}

interface RecentInvoicesProps {
  invoices: Invoice[]
}

export const RecentInvoices: FC<RecentInvoicesProps> = ({ invoices }) => {
  const t = useTranslation()

  const formatAmount = (amount: string) => {
    const cents = parseInt(amount, 10)
    const dollars = cents / 100
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(dollars)
  }

  return (
    <Card>
      <CardHeader>
        <CardTitle>{t("ui.billing.recent_invoices")}</CardTitle>
        <CardDescription>{t("ui.billing.recent_invoices_description")}</CardDescription>
      </CardHeader>
      <CardContent>
        {invoices.length === 0 ? (
          <div className="text-muted-foreground flex items-center justify-center py-8">
            <div className="text-center">
              <Receipt className="mx-auto mb-4 h-12 w-12" />
              <p className="text-sm">{t("ui.billing.no_invoices")}</p>
            </div>
          </div>
        ) : (
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>{t("ui.billing.invoice_date")}</TableHead>
                  <TableHead>{t("ui.billing.invoice_amount")}</TableHead>
                  <TableHead className="text-right">{t("ui.billing.invoice_actions")}</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {invoices.map((invoice) => (
                  <TableRow key={invoice.id}>
                    <TableCell className="font-medium">{format(parseISO(invoice.date), "MMM d, yyyy")}</TableCell>
                    <TableCell>{formatAmount(invoice.total)}</TableCell>
                    <TableCell className="text-right">
                      <div className="flex justify-end space-x-2">
                        <Button size="sm" variant="outline" asChild>
                          <a href={invoice.hosted_invoice_url} target="_blank" rel="noopener noreferrer">
                            <ExternalLink className="mr-2 h-4 w-4" />
                            {t("ui.billing.view_invoice")}
                          </a>
                        </Button>
                        <Button size="sm" variant="outline" asChild>
                          <a href={invoice.invoice_pdf} target="_blank" rel="noopener noreferrer" download>
                            <Download className="mr-2 h-4 w-4" />
                            {t("ui.billing.download_invoice")}
                          </a>
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        )}
      </CardContent>
    </Card>
  )
}
