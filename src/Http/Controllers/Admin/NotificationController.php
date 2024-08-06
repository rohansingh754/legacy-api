<?php

namespace Webkul\API\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Webkul\API\Helpers\SendNotification;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\API\DataGrids\PushNotificationDataGrid;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\API\Repositories\NotificationRepository;
use Webkul\Category\Repositories\CategoryRepository;

class NotificationController extends Controller
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected ChannelRepository $channelRepository,
        protected NotificationRepository $notificationRepository,
        protected SendNotification $sendNotification,
        protected CategoryRepository $categoryRepository,
        protected ProductRepository $productRepository
    ) {
        $this->_config = request('_config');

        $this->middleware('admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(PushNotificationDataGrid::class)->toJson();
        }

        return view('api::notification.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $channels = $this->channelRepository->get();

        return view('api::notification.create', compact('channels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title'     => 'string|required',
            'content'   => 'string|required',
            'image.*'   => 'mimes:jpeg,jpg,bmp,png',
            'type'      => 'required',
            'channels'  => 'required',
            'status'    => 'required',
        ]);

        $data = collect(request()->all())->except('_token')->toArray();

        $this->notificationRepository->create($data);

        session()->flash('success', trans('api::app.alert.create-success', ['name' => 'Notification']));

        return redirect()->route('api.notification.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $notification = $this->notificationRepository->findOrFail($id);

        $channels = $this->channelRepository->get();

        return view('api::notification.edit', compact('notification', 'channels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'title'     => 'string|required',
            'content'   => 'string|required',
            'image.*'   => 'mimes:jpeg,jpg,bmp,png',
            'type'      => 'required',
            'channels'  => 'required',
            'status'    => 'required',
        ]);

        $data = collect(request()->all())->except('_token')->toArray();

        $this->notificationRepository->update($data, $id);

        session()->flash('success', trans('api::app.alert.update-success', ['name' => 'Notification']));

        return redirect()->route('api.notification.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $this->notificationRepository->delete($id);

            return response()->json(['message' => trans('api::app.alert.delete-success', ['name' => 'Notification'])], 200);
        } catch (\Exception $e) {
            session()->flash('success', trans('api::app.alert.delete-failed', ['name' => 'Notification']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * To mass update the notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate(MassUpdateRequest $request): JsonResponse
    {
        $this->notificationRepository
            ->whereIn('id', $request->indices)
            ->update(['status' => $request->value]);

        return new JsonResponse([
            'message' => trans('api::app.alert.update-success', ['name' => 'Notification']),
        ]);
    }

    /**
     * To mass delete the notificaton.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(MassDestroyRequest $request): JsonResponse
    {
        try {
            $this->notificationRepository->whereIn('id', $request->indices)->delete();

            return new JsonResponse([
                'message' => trans('api::app.alert.delete-success', ['name' => 'Notification']),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => trans('api::app.alert.delete-failed', ['name' => 'Notification']),
            ], 500);
        }
    }

    /**
     * To sent the notification to the device.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendNotification($id)
    {
        $data = $this->notificationRepository->find($id);

        $notification = $this->sendNotification->sendGCM($data);
        Log::info($notification);
        if (isset($notification->message_id)) {
            session()->flash('success', trans('api::app.alert.sended-successfully', ['name' => 'Notification']));
        } else {
            session()->flash('error', trans('api::app.alert.sended-fails', ['name' => 'Notification']));
        }

        return redirect()->back();
    }

    /**
     * To check resource exist in DB.
     *
     * @return \Illuminate\Http\Response
     */
    public function exist()
    {
        $data = request()->all();

        if (substr_count($data['givenValue'], ' ') > 0) {
            return response()->json(['value' => false, 'message' => 'Product not exist', 'type' => $data['selectedType']], 200);
        }

        //product case
        if ($data['selectedType'] == 'product') {
            if ($product = $this->productRepository->find($data['givenValue'])) {

                if (!isset($product->id) || !isset($product->url_key) || (isset($product->parent_id) && $product->parent_id)) {
                    return response()->json(['value' => false, 'message' => 'Product not exist', 'type' => 'product'], 200);
                } else {
                    return response()->json(['value' => true], 200);
                }
            } else {
                return response()->json(['value' => false, 'message' => 'Product not exist', 'type' => 'product'], 200);
            }
        }

        //category case
        if ($data['selectedType'] == 'category') {
            if ($this->categoryRepository->find($data['givenValue'])) {
                return response()->json(['value' => true], 200);
            } else {
                return response()->json(['value' => false, 'message' => 'Category not exist', 'type' => 'category'], 200);
            }
        }
    }
}
