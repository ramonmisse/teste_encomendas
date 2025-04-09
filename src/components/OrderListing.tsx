import React, { useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
  HoverCard,
  HoverCardContent,
  HoverCardTrigger,
} from "@/components/ui/hover-card";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import { Download, Edit, Eye, Image, Trash2 } from "lucide-react";

interface Order {
  id: string;
  salesRep: string;
  client: string;
  model: string;
  metalType: "Gold" | "Silver" | "Not Applicable";
  deliveryDate: string;
  notes?: string;
  imageUrl?: string;
}

const OrderListing = ({ orders = mockOrders }: { orders?: Order[] }) => {
  const [previewImage, setPreviewImage] = useState<string | null>(null);
  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);
  const [selectedOrderId, setSelectedOrderId] = useState<string | null>(null);
  const [viewOrderDialogOpen, setViewOrderDialogOpen] = useState(false);
  const [selectedOrder, setSelectedOrder] = useState<Order | null>(null);

  const handleViewOrder = (order: Order) => {
    setSelectedOrder(order);
    setViewOrderDialogOpen(true);
  };

  const handleDeleteClick = (orderId: string) => {
    setSelectedOrderId(orderId);
    setDeleteDialogOpen(true);
  };

  const handleDeleteConfirm = () => {
    // In a real implementation, this would call an API to delete the order
    console.log(`Deleting order ${selectedOrderId}`);
    setDeleteDialogOpen(false);
    setSelectedOrderId(null);
  };

  const handleDownload = (imageUrl: string) => {
    // In a real implementation, this would trigger a download of the image
    console.log(`Downloading image from ${imageUrl}`);
  };

  return (
    <div className="bg-white p-6 rounded-lg shadow-sm w-full">
      <Card>
        <CardHeader>
          <CardTitle className="text-2xl font-bold">Order Listing</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="rounded-md border">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Sales Representative</TableHead>
                  <TableHead>Client</TableHead>
                  <TableHead>Model</TableHead>
                  <TableHead>Metal Type</TableHead>
                  <TableHead>Delivery Date</TableHead>
                  <TableHead>Image</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {orders.map((order) => (
                  <TableRow key={order.id}>
                    <TableCell>{order.salesRep}</TableCell>
                    <TableCell>{order.client}</TableCell>
                    <TableCell>{order.model}</TableCell>
                    <TableCell>{order.metalType}</TableCell>
                    <TableCell>
                      {new Date(order.deliveryDate).toLocaleDateString()}
                    </TableCell>
                    <TableCell>
                      {order.imageUrl ? (
                        <HoverCard>
                          <HoverCardTrigger asChild>
                            <Button variant="ghost" size="icon">
                              <Image className="h-5 w-5" />
                            </Button>
                          </HoverCardTrigger>
                          <HoverCardContent className="w-80">
                            <div className="flex justify-center">
                              <img
                                src={order.imageUrl}
                                alt="Order reference"
                                className="max-h-60 object-contain rounded-md"
                              />
                            </div>
                          </HoverCardContent>
                        </HoverCard>
                      ) : (
                        <span className="text-gray-400">No image</span>
                      )}
                    </TableCell>
                    <TableCell>
                      <div className="flex space-x-2">
                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                variant="outline"
                                size="icon"
                                onClick={() => handleViewOrder(order)}
                              >
                                <Eye className="h-4 w-4" />
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>View Order</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>

                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button variant="outline" size="icon">
                                <Edit className="h-4 w-4" />
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Edit Order</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>

                        {order.imageUrl && (
                          <TooltipProvider>
                            <Tooltip>
                              <TooltipTrigger asChild>
                                <Button
                                  variant="outline"
                                  size="icon"
                                  onClick={() =>
                                    handleDownload(order.imageUrl || "")
                                  }
                                >
                                  <Download className="h-4 w-4" />
                                </Button>
                              </TooltipTrigger>
                              <TooltipContent>
                                <p>Download Image</p>
                              </TooltipContent>
                            </Tooltip>
                          </TooltipProvider>
                        )}

                        <TooltipProvider>
                          <Tooltip>
                            <TooltipTrigger asChild>
                              <Button
                                variant="outline"
                                size="icon"
                                className="text-red-500 hover:bg-red-50"
                                onClick={() => handleDeleteClick(order.id)}
                              >
                                <Trash2 className="h-4 w-4" />
                              </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                              <p>Delete Order</p>
                            </TooltipContent>
                          </Tooltip>
                        </TooltipProvider>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </CardContent>
      </Card>

      {/* View Order Dialog */}
      <Dialog open={viewOrderDialogOpen} onOpenChange={setViewOrderDialogOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Order Details</DialogTitle>
          </DialogHeader>
          {selectedOrder && (
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <p className="text-sm font-medium">Sales Representative:</p>
                  <p className="text-sm">{selectedOrder.salesRep}</p>
                </div>
                <div>
                  <p className="text-sm font-medium">Client:</p>
                  <p className="text-sm">{selectedOrder.client}</p>
                </div>
                <div>
                  <p className="text-sm font-medium">Model:</p>
                  <p className="text-sm">{selectedOrder.model}</p>
                </div>
                <div>
                  <p className="text-sm font-medium">Metal Type:</p>
                  <p className="text-sm">{selectedOrder.metalType}</p>
                </div>
                <div>
                  <p className="text-sm font-medium">Delivery Date:</p>
                  <p className="text-sm">
                    {new Date(selectedOrder.deliveryDate).toLocaleDateString()}
                  </p>
                </div>
              </div>

              {selectedOrder.notes && (
                <div>
                  <p className="text-sm font-medium">Notes:</p>
                  <p className="text-sm">{selectedOrder.notes}</p>
                </div>
              )}

              {selectedOrder.imageUrl && (
                <div>
                  <p className="text-sm font-medium">Reference Image:</p>
                  <div className="mt-2 flex justify-center">
                    <img
                      src={selectedOrder.imageUrl}
                      alt="Order reference"
                      className="max-h-60 object-contain rounded-md"
                    />
                  </div>
                  <div className="mt-2 flex justify-end">
                    <Button
                      variant="outline"
                      size="sm"
                      onClick={() =>
                        handleDownload(selectedOrder.imageUrl || "")
                      }
                    >
                      <Download className="h-4 w-4 mr-2" />
                      Download Image
                    </Button>
                  </div>
                </div>
              )}
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Delete Confirmation Dialog */}
      <AlertDialog open={deleteDialogOpen} onOpenChange={setDeleteDialogOpen}>
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete the
              order and all associated data.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel>Cancel</AlertDialogCancel>
            <AlertDialogAction
              onClick={handleDeleteConfirm}
              className="bg-red-500 hover:bg-red-600"
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

// Mock data for demonstration
const mockOrders: Order[] = [
  {
    id: "1",
    salesRep: "Jane Smith",
    client: "Maria Johnson",
    model: "Pendant Necklace",
    metalType: "Gold",
    deliveryDate: "2023-06-15T10:00:00",
    notes: "Client wants a custom engraving on the back",
    imageUrl:
      "https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=500&q=80",
  },
  {
    id: "2",
    salesRep: "Emily Davis",
    client: "Sarah Williams",
    model: "Stud Earrings",
    metalType: "Silver",
    deliveryDate: "2023-06-20T14:30:00",
    imageUrl:
      "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=500&q=80",
  },
  {
    id: "3",
    salesRep: "Michael Brown",
    client: "Robert Taylor",
    model: "Custom Ring",
    metalType: "Gold",
    deliveryDate: "2023-06-25T11:00:00",
    notes: "Size 7, with diamond inlay",
    imageUrl:
      "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=500&q=80",
  },
  {
    id: "4",
    salesRep: "Lisa Johnson",
    client: "Jennifer Garcia",
    model: "Charm Bracelet",
    metalType: "Silver",
    deliveryDate: "2023-06-30T15:00:00",
  },
  {
    id: "5",
    salesRep: "David Wilson",
    client: "Thomas Martinez",
    model: "Cufflinks",
    metalType: "Not Applicable",
    deliveryDate: "2023-07-05T09:30:00",
    notes: "Custom logo engraving",
    imageUrl:
      "https://images.unsplash.com/photo-1600721391776-b5cd0e0048f9?w=500&q=80",
  },
];

export default OrderListing;
