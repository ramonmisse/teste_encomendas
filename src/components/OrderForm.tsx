import React, { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import { format } from "date-fns";
import { Calendar as CalendarIcon, Upload, X } from "lucide-react";

import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardContent } from "@/components/ui/card";

const formSchema = z.object({
  salesRepresentative: z.string({
    required_error: "Please select a sales representative",
  }),
  clientName: z.string().min(2, {
    message: "Client name must be at least 2 characters.",
  }),
  deliveryDate: z.date({
    required_error: "Please select a delivery date",
  }),
  model: z.string({
    required_error: "Please select a model",
  }),
  metalType: z.enum(["gold", "silver", "notApplicable"], {
    required_error: "Please select a metal type",
  }),
  notes: z.string().optional(),
});

type OrderFormValues = z.infer<typeof formSchema>;

interface OrderFormProps {
  onSubmit?: (data: OrderFormValues & { images: File[] }) => void;
  initialData?: Partial<OrderFormValues>;
}

const OrderForm = ({ onSubmit, initialData }: OrderFormProps = {}) => {
  const [images, setImages] = useState<File[]>([]);
  const [selectedModel, setSelectedModel] = useState<string | null>(
    initialData?.model || null,
  );

  const form = useForm<OrderFormValues>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      salesRepresentative: initialData?.salesRepresentative || "",
      clientName: initialData?.clientName || "",
      deliveryDate: initialData?.deliveryDate || new Date(),
      model: initialData?.model || "",
      metalType: initialData?.metalType || "notApplicable",
      notes: initialData?.notes || "",
    },
  });

  // Mock data - in a real app, these would come from an API
  const salesReps = [
    { id: "1", name: "Ana Silva" },
    { id: "2", name: "Maria Oliveira" },
    { id: "3", name: "Juliana Santos" },
  ];

  const models = [
    {
      id: "1",
      name: "Anel Solitário",
      imageUrl:
        "https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=300&q=80",
    },
    {
      id: "2",
      name: "Brinco Argola",
      imageUrl:
        "https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=300&q=80",
    },
    {
      id: "3",
      name: "Colar Pingente",
      imageUrl:
        "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=300&q=80",
    },
    {
      id: "4",
      name: "Pulseira Corrente",
      imageUrl:
        "https://images.unsplash.com/photo-1611652022419-a9419f74343d?w=300&q=80",
    },
  ];

  const handleImageUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const newFiles = Array.from(e.target.files);
      setImages((prev) => [...prev, ...newFiles]);
    }
  };

  const removeImage = (index: number) => {
    setImages((prev) => prev.filter((_, i) => i !== index));
  };

  const handleSubmit = (values: OrderFormValues) => {
    if (onSubmit) {
      onSubmit({ ...values, images });
    }
    console.log({ ...values, images });
  };

  const handleModelChange = (modelId: string) => {
    setSelectedModel(modelId);
    form.setValue("model", modelId);
  };

  return (
    <div className="w-full max-w-4xl mx-auto p-6 bg-background">
      <h1 className="text-2xl font-bold mb-6">Create New Order</h1>

      <Form {...form}>
        <form onSubmit={form.handleSubmit(handleSubmit)} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Sales Representative */}
            <FormField
              control={form.control}
              name="salesRepresentative"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Sales Representative</FormLabel>
                  <Select
                    onValueChange={field.onChange}
                    defaultValue={field.value}
                  >
                    <FormControl>
                      <SelectTrigger>
                        <SelectValue placeholder="Select a sales representative" />
                      </SelectTrigger>
                    </FormControl>
                    <SelectContent>
                      {salesReps.map((rep) => (
                        <SelectItem key={rep.id} value={rep.id}>
                          {rep.name}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <FormMessage />
                </FormItem>
              )}
            />

            {/* Client Name */}
            <FormField
              control={form.control}
              name="clientName"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Client Name</FormLabel>
                  <FormControl>
                    <Input placeholder="Enter client name" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            {/* Current Date (Read-only) */}
            <FormItem>
              <FormLabel>Order Date</FormLabel>
              <FormControl>
                <Input value={format(new Date(), "PPP p")} disabled />
              </FormControl>
              <FormDescription>
                Current date and time (automatically set)
              </FormDescription>
            </FormItem>

            {/* Delivery Date */}
            <FormField
              control={form.control}
              name="deliveryDate"
              render={({ field }) => (
                <FormItem className="flex flex-col">
                  <FormLabel>Delivery Date</FormLabel>
                  <Popover>
                    <PopoverTrigger asChild>
                      <FormControl>
                        <Button
                          variant={"outline"}
                          className={cn(
                            "w-full pl-3 text-left font-normal",
                            !field.value && "text-muted-foreground",
                          )}
                        >
                          {field.value ? (
                            format(field.value, "PPP")
                          ) : (
                            <span>Pick a date</span>
                          )}
                          <CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
                        </Button>
                      </FormControl>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={field.value}
                        onSelect={field.onChange}
                        disabled={(date) => date < new Date()}
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                  <FormMessage />
                </FormItem>
              )}
            />
          </div>

          {/* Model Selection */}
          <FormField
            control={form.control}
            name="model"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Product Model</FormLabel>
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mt-2">
                  {models.map((model) => (
                    <Card
                      key={model.id}
                      className={cn(
                        "cursor-pointer transition-all hover:shadow-md",
                        selectedModel === model.id && "ring-2 ring-primary",
                      )}
                      onClick={() => handleModelChange(model.id)}
                    >
                      <CardContent className="p-3">
                        <div className="aspect-square overflow-hidden rounded-md mb-2">
                          <img
                            src={model.imageUrl}
                            alt={model.name}
                            className="w-full h-full object-cover"
                          />
                        </div>
                        <p className="text-center font-medium">{model.name}</p>
                      </CardContent>
                    </Card>
                  ))}
                </div>
                <FormMessage />
              </FormItem>
            )}
          />

          {/* Metal Type */}
          <FormField
            control={form.control}
            name="metalType"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Metal Type</FormLabel>
                <Select
                  onValueChange={field.onChange}
                  defaultValue={field.value}
                >
                  <FormControl>
                    <SelectTrigger>
                      <SelectValue placeholder="Select metal type" />
                    </SelectTrigger>
                  </FormControl>
                  <SelectContent>
                    <SelectItem value="gold">Gold</SelectItem>
                    <SelectItem value="silver">Silver</SelectItem>
                    <SelectItem value="notApplicable">
                      Not Applicable
                    </SelectItem>
                  </SelectContent>
                </Select>
                <FormMessage />
              </FormItem>
            )}
          />

          {/* Image Upload */}
          <FormItem>
            <FormLabel>Customization Photos</FormLabel>
            <div className="flex items-center gap-4">
              <label
                htmlFor="image-upload"
                className="cursor-pointer flex items-center gap-2 px-4 py-2 border border-input rounded-md hover:bg-accent"
              >
                <Upload className="h-4 w-4" />
                <span>Upload Images</span>
                <input
                  id="image-upload"
                  type="file"
                  multiple
                  accept="image/*"
                  className="hidden"
                  onChange={handleImageUpload}
                />
              </label>
              <p className="text-sm text-muted-foreground">
                {images.length} {images.length === 1 ? "file" : "files"}{" "}
                selected
              </p>
            </div>

            {/* Preview uploaded images */}
            {images.length > 0 && (
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mt-4">
                {images.map((image, index) => (
                  <div key={index} className="relative group">
                    <div className="aspect-square overflow-hidden rounded-md border">
                      <img
                        src={URL.createObjectURL(image)}
                        alt={`Upload ${index + 1}`}
                        className="w-full h-full object-cover"
                      />
                    </div>
                    <button
                      type="button"
                      onClick={() => removeImage(index)}
                      className="absolute top-1 right-1 bg-background/80 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  </div>
                ))}
              </div>
            )}
          </FormItem>

          {/* Notes */}
          <FormField
            control={form.control}
            name="notes"
            render={({ field }) => (
              <FormItem>
                <FormLabel>Notes</FormLabel>
                <FormControl>
                  <Textarea
                    placeholder="Add any additional information about the order"
                    className="min-h-[120px]"
                    {...field}
                  />
                </FormControl>
                <FormMessage />
              </FormItem>
            )}
          />

          {/* Submit Button */}
          <div className="flex justify-end">
            <Button type="submit" size="lg">
              Save Order
            </Button>
          </div>
        </form>
      </Form>
    </div>
  );
};

export default OrderForm;
